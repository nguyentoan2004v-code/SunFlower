<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SemanticSearchService
 *
 * Dịch vụ tìm kiếm ngữ nghĩa sử dụng mô hình
 * dangvantuan/vietnamese-embedding từ HuggingFace API.
 *
 * Luồng hoạt động:
 * 1. Nhận từ khóa tìm kiếm của người dùng
 * 2. Gọi HuggingFace API để chuyển từ khóa thành vector (embedding)
 * 3. Tính độ tương đồng (cosine similarity) giữa vector từ khóa
 *    và vector của từng sản phẩm đã được lưu trong DB
 * 4. Trả về danh sách sản phẩm được sắp xếp theo mức độ phù hợp
 */
class SemanticSearchService
{
    /**
     * URL của HuggingFace Inference API cho model vietnamese-embedding.
     * Model này chuyên xử lý tiếng Việt, dựa trên PhoBERT.
     *
     * ⚠️ Lưu ý: HuggingFace đã đổi endpoint từ api-inference sang router (2024+)
     * URL cũ (không còn dùng): https://api-inference.huggingface.co/models/...
     * URL mới (đang dùng):     https://router.huggingface.co/hf-inference/models/dangvantuan/vietnamese-embedding/pipeline/feature-extraction
     */
    private string $apiUrl = 'https://router.huggingface.co/hf-inference/models/dangvantuan/vietnamese-embedding/pipeline/feature-extraction';

    /**
     * API Token lấy từ .env (HUGGINGFACE_API_TOKEN)
     */
    private ?string $apiToken;

    /**
     * Timeout (giây) khi gọi HuggingFace API.
     * Cold start thường mất 20-40 giây, đặt 45 để an toàn.
     */
    private int $timeout = 45;

    /**
     * Số giây cache vector của từ khóa.
     * Cùng 1 từ khóa được tìm nhiều lần → dùng cache, không gọi API lại.
     */
    private int $cacheTtl = 3600; // 1 giờ

    /**
     * Ngưỡng điểm tương đồng tối thiểu (0.0 - 1.0).
     * Sản phẩm có điểm < ngưỡng này sẽ bị loại khỏi kết quả.
     */
    private float $similarityThreshold = 0.3;

    public function __construct()
    {
        $this->apiToken = config('services.huggingface.token');
    }

    // =========================================================
    // PUBLIC METHODS
    // =========================================================

    /**
     * Tìm kiếm sản phẩm theo ngữ nghĩa.
     *
     * @param  string  $keyword  Từ khóa người dùng nhập
     * @param  \Illuminate\Database\Eloquent\Collection  $allProducts  Toàn bộ sản phẩm từ DB
     * @return array{products: array, search_type: string}
     *         - products:    Mảng sản phẩm đã sắp xếp theo điểm tương đồng
     *         - search_type: 'semantic' | 'fallback' để View hiển thị badge tương ứng
     */
    public function search(string $keyword, $allProducts): array
    {
        // Không đủ điều kiện gọi AI → dùng fallback ngay
        if (! $this->isAvailable()) {
            Log::info('[SemanticSearch] API token chưa cấu hình, dùng LIKE search.');
            return $this->fallbackSearch($keyword, $allProducts);
        }

        try {
            // 1. Lấy embedding vector cho từ khóa (có cache)
            $queryVector = $this->getEmbedding($keyword);

            if (empty($queryVector)) {
                Log::warning('[SemanticSearch] Embedding trả về rỗng cho: ' . $keyword);
                return $this->fallbackSearch($keyword, $allProducts);
            }

            // 2. Tính cosine similarity với từng sản phẩm
            $scored = [];
            foreach ($allProducts as $product) {
                $productVector = $this->getProductEmbedding($product);

                if (empty($productVector)) {
                    continue;
                }

                $score = $this->cosineSimilarity($queryVector, $productVector);

                // Chỉ giữ sản phẩm có điểm >= ngưỡng
                if ($score >= $this->similarityThreshold) {
                    $scored[] = [
                        'product' => $product,
                        'score'   => $score,
                    ];
                }
            }

            // 3. Sắp xếp từ cao đến thấp
            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

            $sortedProducts = array_map(fn($item) => $item['product'], $scored);

            Log::info('[SemanticSearch] Tìm "' . $keyword . '" → ' . count($sortedProducts) . ' kết quả (semantic).');

            return [
                'products'    => $sortedProducts,
                'search_type' => 'semantic',
            ];

        } catch (\Exception $e) {
            Log::error('[SemanticSearch] Lỗi: ' . $e->getMessage());
            return $this->fallbackSearch($keyword, $allProducts);
        }
    }

    /**
     * Kiểm tra xem service có sẵn sàng gọi API không.
     */
    public function isAvailable(): bool
    {
        return ! empty($this->apiToken);
    }

    // =========================================================
    // EMBEDDING METHODS
    // =========================================================

    /**
     * Lấy embedding vector cho một đoạn văn bản.
     * Kết quả được cache để tránh gọi API nhiều lần với cùng nội dung.
     *
     * @param  string  $text  Văn bản cần nhúng (từ khóa hoặc tên sản phẩm)
     * @return array  Vector 768 chiều (float)
     */
    public function getEmbedding(string $text): array
    {
        // Tạo cache key an toàn từ nội dung văn bản
        $cacheKey = 'hf_embedding_' . md5(mb_strtolower(trim($text)));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($text) {
            return $this->callHuggingFaceApi($text);
        });
    }

    /**
     * Lấy embedding của sản phẩm.
     * Sử dụng tensp + mota để vector mang nhiều ngữ nghĩa hơn.
     *
     * @param  \App\Models\SanPham  $product
     * @return array
     */
    private function getProductEmbedding($product): array
    {
        // Kết hợp tên + mô tả ngắn để tăng độ chính xác
        $text = $product->tensp;
        if (! empty($product->mota)) {
            // Chỉ lấy 200 ký tự đầu của mô tả để tránh quá dài
            $text .= ' ' . mb_substr(strip_tags($product->mota), 0, 200);
        }

        $cacheKey = 'hf_product_emb_' . $product->masp;

        return Cache::remember($cacheKey, $this->cacheTtl * 24, function () use ($text) {
            return $this->callHuggingFaceApi($text);
        });
    }

    /**
     * Gọi HuggingFace Inference API để lấy embedding vector.
     *
     * @param  string  $text
     * @return array  Vector hoặc mảng rỗng nếu lỗi
     */
    private function callHuggingFaceApi(string $text): array
    {
        $response = Http::withToken($this->apiToken)
            ->timeout($this->timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->apiUrl, [
                'inputs'  => $text,
                'options' => ['wait_for_model' => true], // Chờ model khởi động (cold start)
            ]);

        if (! $response->successful()) {
            $body = $response->body();
            Log::warning('[SemanticSearch] HuggingFace trả về lỗi: ' . $body);
            return [];
        }

        $data = $response->json();

        // API trả về mảng lồng nhau: [[...vector...]]
        // Cần lấy phần tử đầu tiên
        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }

        // Một số model trả về mảng phẳng: [...vector...]
        if (is_array($data) && isset($data[0]) && is_numeric($data[0])) {
            return $data;
        }

        return [];
    }

    // =========================================================
    // MATH METHODS
    // =========================================================

    /**
     * Tính Cosine Similarity giữa 2 vector.
     *
     * Công thức: cos(θ) = (A · B) / (|A| × |B|)
     * Kết quả từ -1 (ngược chiều) đến 1 (cùng chiều/giống nhau)
     * Với embedding, thường từ 0 đến 1.
     *
     * @param  array  $vectorA
     * @param  array  $vectorB
     * @return float  Điểm từ 0.0 đến 1.0
     */
    private function cosineSimilarity(array $vectorA, array $vectorB): float
    {
        if (count($vectorA) !== count($vectorB) || empty($vectorA)) {
            return 0.0;
        }

        $dotProduct  = 0.0;
        $magnitudeA  = 0.0;
        $magnitudeB  = 0.0;

        $length = count($vectorA);
        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $vectorA[$i] * $vectorB[$i];
            $magnitudeA += $vectorA[$i] * $vectorA[$i];
            $magnitudeB += $vectorB[$i] * $vectorB[$i];
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        // Tránh chia cho 0
        if ($magnitudeA == 0.0 || $magnitudeB == 0.0) {
            return 0.0;
        }

        return (float) ($dotProduct / ($magnitudeA * $magnitudeB));
    }

    // =========================================================
    // FALLBACK — Smart Search (Offline, không cần internet)
    // =========================================================

    /**
     * Tìm kiếm thông minh offline với từ đồng nghĩa và từ liên quan.
     * Hoạt động khi HuggingFace API không khả dụng hoặc mạng bị chặn.
     *
     * Cơ chế:
     * 1. Tách từ khóa → từng token nhỏ
     * 2. Mở rộng bằng synonym map tiếng Việt (từ đồng nghĩa + liên quan)
     * 3. Tìm kiếm bất kỳ token nào khớp trong tensp / mota
     * 4. Tính điểm: khớp nhiều từ hơn → đứng trên
     *
     * @param  string  $keyword
     * @param  \Illuminate\Database\Eloquent\Collection  $allProducts
     * @return array
     */
    private function fallbackSearch(string $keyword, $allProducts): array
    {
        $keyword_lower = mb_strtolower(trim($keyword));

        // 1. Lấy tất cả token cần tìm (từ gốc + từ đồng nghĩa)
        $searchTokens = $this->expandKeywords($keyword_lower);

        $scored = [];

        foreach ($allProducts as $product) {
            $productText = mb_strtolower(
                $product->tensp . ' ' . ($product->mota ?? '') . ' ' . ($product->mota_chitiet ?? '')
            );

            $score = 0;

            foreach ($searchTokens as $token) {
                if (mb_strlen($token) < 2) {
                    continue; // bỏ qua token quá ngắn
                }

                if (str_contains($productText, $token)) {
                    // Từ xuất hiện trong tên sản phẩm → điểm cao hơn
                    if (str_contains(mb_strtolower($product->tensp), $token)) {
                        $score += 3;
                    } else {
                        $score += 1;
                    }
                }
            }

            if ($score > 0) {
                $scored[] = ['product' => $product, 'score' => $score];
            }
        }

        // Sắp xếp theo điểm
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        $sortedProducts = array_map(fn($item) => $item['product'], $scored);

        Log::info('[SemanticSearch] Smart fallback cho: "' . $keyword . '" với tokens: [' . implode(', ', $searchTokens) . '] → ' . count($sortedProducts) . ' kết quả.');

        return [
            'products'    => $sortedProducts,
            'search_type' => 'fallback',
        ];
    }

    /**
     * Mở rộng từ khóa bằng synonym map tiếng Việt.
     * Ví dụ: "sinh nhật" → ["sinh nhật", "birthday", "hồng", "lily", "tulip"]
     *
     * @param  string  $keyword
     * @return array  Mảng các token cần tìm
     */
    private function expandKeywords(string $keyword): array
    {
        // Tách từ khóa gốc thành các token
        $originalTokens = array_filter(explode(' ', $keyword), fn($t) => mb_strlen(trim($t)) > 1);

        // Từ điển đồng nghĩa / liên quan cho lĩnh vực hoa
        $synonymMap = [
            // === Dịp tặng quà ===
            'sinh nhật'       => ['hồng', 'lily', 'tulip', 'hướng dương', 'cẩm chướng', 'bó hoa'],
            'birthday'        => ['hồng', 'lily', 'tulip', 'hướng dương', 'sinh nhật'],
            'valentine'       => ['hồng đỏ', 'hồng', 'tình yêu', 'tim'],
            'tình yêu'        => ['hồng đỏ', 'hồng', 'valentine'],
            'người yêu'       => ['hồng đỏ', 'hồng', 'tình yêu', 'valentine'],
            'kỷ niệm'         => ['hồng', 'lan', 'lily'],
            'mẹ'              => ['mẫu đơn', 'lan', 'cẩm chướng', 'hướng dương'],
            'bà'              => ['mẫu đơn', 'lan', 'cẩm chướng'],
            'phụ nữ'          => ['hồng', 'lan', 'cẩm chướng', 'mẫu đơn'],
            '8 3'             => ['hồng', 'lan', 'cẩm chướng', 'mẫu đơn', 'hướng dương'],
            '20 10'           => ['hồng', 'lan', 'cẩm chướng', 'mẫu đơn'],
            'khai trương'     => ['hoa giỏ', 'lan hồ điệp', 'lan', 'bình hoa'],
            'đám cưới'        => ['hồng trắng', 'lan', 'cẩm chướng trắng', 'cô dâu'],
            'chia buồn'       => ['cúc trắng', 'huệ', 'lan trắng'],
            'tang lễ'         => ['cúc trắng', 'huệ', 'lan trắng'],
            'tốt nghiệp'      => ['hướng dương', 'hồng', 'tulip'],
            'chúc mừng'       => ['hướng dương', 'hồng', 'tulip', 'lily'],
            'thăm bệnh'       => ['hướng dương', 'cúc vàng', 'cẩm chướng'],

            // === Màu sắc / Cảm xúc ===
            'đỏ'              => ['hồng đỏ', 'cẩm chướng đỏ', 'tulip đỏ'],
            'trắng'           => ['hồng trắng', 'cúc trắng', 'lan trắng', 'huệ'],
            'vàng'            => ['hướng dương', 'cúc vàng', 'hồng vàng'],
            'tím'             => ['hoa tím', 'oải hương', 'violet'],
            'hồng'            => ['hồng nhạt', 'hoa hồng'],
            'buồn'            => ['cúc trắng', 'huệ', 'lan trắng'],
            'vui'             => ['hướng dương', 'tulip', 'hồng vàng'],
            'đẹp'             => ['hồng', 'lan', 'tulip', 'lily'],

            // === Loại hoa (viết tắt / tiếng Anh) ===
            'rose'            => ['hồng', 'hoa hồng'],
            'sunflower'       => ['hướng dương'],
            'lily'            => ['lily', 'loa kèn'],
            'tulip'           => ['tulip'],
            'orchid'          => ['lan', 'lan hồ điệp'],
            'daisy'           => ['cúc'],
            'flower'          => ['hoa', 'bó hoa'],
            'hoa tươi'        => ['hồng', 'lan', 'cúc', 'hướng dương'],

            // === Tính chất ===
            'tươi'            => ['hoa tươi', 'hồng', 'lan'],
            'thơm'            => ['hồng', 'lan', 'oải hương'],
            'lâu'             => ['hoa sáp', 'hoa khô', 'hoa lụa'],
            'bền'             => ['hoa sáp', 'hoa khô'],
            'sang'            => ['lan hồ điệp', 'hồng', 'lily'],
            'sang trọng'      => ['lan hồ điệp', 'hồng đỏ', 'lily'],
            'rẻ'              => ['cúc', 'hoa mix', 'hoa đồng tiền'],
            'mix'             => ['hoa mix', 'bó hoa mix'],
            'bó'              => ['bó hoa', 'hoa'],
            'giỏ'             => ['giỏ hoa', 'hoa giỏ'],
            'lẵng'            => ['lẵng hoa', 'hoa'],
        ];

        $expandedTokens = array_values($originalTokens);

        foreach ($originalTokens as $token) {
            $token = trim($token);

            // Tìm trong synonym map
            foreach ($synonymMap as $key => $synonyms) {
                if (str_contains($keyword, $key) || $token === $key) {
                    $expandedTokens = array_merge($expandedTokens, $synonyms);
                }
            }
        }

        // Loại bỏ trùng lặp và làm sạch
        return array_values(array_unique(array_filter($expandedTokens, fn($t) => mb_strlen(trim($t)) > 1)));
    }
}

