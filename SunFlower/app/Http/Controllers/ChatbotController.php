<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $userMessage = trim($request->message);

        // ==========================================
        // 1. BỘ LỌC TỪ KHÓA (PRE-FILTER) CHỐNG PROMPT INJECTION
        // ==========================================
        // Chặn ngay các cụm từ thao túng phổ biến bằng regex
        $forbiddenPatterns = [
            '/bỏ qua/i', '/ignore/i', '/quên/i', '/hướng dẫn trước/i',
            '/prompt/i', '/mã giảm giá/i', '/discount code/i', '/free/i',
            '/hack/i', '/system/i', '/jailbreak/i', '/làm ngơ/i'
        ];

        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $userMessage)) {
                return response()->json([
                    'success' => true,
                    'reply' => 'Dạ, em chỉ là nhân viên tư vấn hoa của SunFlower, em không hiểu và không thể thực hiện yêu cầu này của quý khách ạ. Quý khách có muốn tham khảo các mẫu hoa mới nhất không?'
                ]);
            }
        }

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json(['error' => 'Hệ thống đang bảo trì. Vui lòng thử lại sau.'], 500);
        }

        // ==========================================
        // 2. CƠ CHẾ RAG (TÌM KIẾM NGỮ NGHĨA & LỌC TỪ KHÓA)
        // ==========================================
        $query = \App\Models\SanPham::query();
        
        // Trích xuất mức giá dự kiến từ tin nhắn (vd: "500k", "1 triệu")
        if (preg_match('/(\d+)\s*(k|ngàn|nghìn)/i', $userMessage, $matches)) {
            $maxPrice = intval($matches[1]) * 1000;
            // Cho phép biên độ dao động +100k
            $query->where('giaban', '<=', $maxPrice + 100000);
        } elseif (preg_match('/(\d+)\s*(tr|triệu)/i', $userMessage, $matches)) {
            $maxPrice = intval($matches[1]) * 1000000;
            $query->where('giaban', '<=', $maxPrice + 200000);
        }

        // Tìm từ khóa loại hoa phổ biến
        $flowers = ['hồng', 'hướng dương', 'cẩm chướng', 'cẩm tú cầu', 'tulip', 'baby', 'lan', 'thạch thảo'];
        $hasFlowerKeyword = false;
        foreach ($flowers as $flower) {
            if (mb_stripos($userMessage, $flower) !== false) {
                $query->where('tensp', 'LIKE', '%' . $flower . '%');
                $hasFlowerKeyword = true;
                break;
            }
        }

        $products = $query->select('masp', 'tensp', 'giaban')->limit(15)->get();
        
        // Fallback: Nếu không tìm thấy hoặc người dùng chỉ chào hỏi chung chung, lấy các sản phẩm nổi bật
        if ($products->isEmpty()) {
            $products = \App\Models\SanPham::select('masp', 'tensp', 'giaban')->inRandomOrder()->limit(10)->get();
        }

        $productListText = "";
        foreach($products as $p) {
            $price = number_format($p->giaban, 0, ',', '.');
            $url = url('/chi-tiet/' . $p->masp);
            $productListText .= "- [{$p->tensp}]({$url}) (Giá: {$price} VNĐ)\n";
        }

        // ==========================================
        // 3. HARD CONTEXT INJECTION & CHAT MEMORY
        // ==========================================
        $systemInstruction = <<<EOT
Bạn là "Hoa" - Trợ lý ảo tư vấn khách hàng chính thức của cửa hàng hoa tươi SunFlower.
Luôn xưng là "em" hoặc "mình" và gọi khách hàng là "Quý khách" hoặc "Anh/Chị/Bạn". Thái độ luôn lễ phép, vui vẻ, nhiệt tình và lãng mạn.

QUY CHUẨN CỬA HÀNG BẮT BUỘC TUÂN THỦ:
1. Giao hàng: Giao hàng hỏa tốc trong 2-4 tiếng nội thành TP.HCM. Cửa hàng hoạt động từ 7h - 20h.
2. Sản phẩm: Chỉ bán các loại hoa tươi. Không bán hoa giả.
3. Giá cả: Tất cả khuyến mãi đều được niêm yết công khai. Giá bó hoa thường dao động từ 300k - 2 triệu.
4. Gắn Link: KHI GỢI Ý MỘT SẢN PHẨM TRONG DANH SÁCH, BẮT BUỘC PHẢI CHÈN ĐƯỜNG DẪN BẰNG MARKDOWN (vd: [Tên Hoa](URL)). Không để lộ URL trần.

DANH SÁCH SẢN PHẨM PHÙ HỢP HIỆN CÓ TẠI CỬA HÀNG (Dùng danh sách này để tư vấn):
{$productListText}

LỆNH CẤM BỊA ĐẶT (STRICT FALLBACK):
Nếu khách hỏi về một loại hoa hoặc mức giá không có trong Danh sách trên, tuyệt đối không tự bịa ra. Phải đáp: "Dạ, hiện tại cửa hàng không có sẵn mẫu này. Quý khách vui lòng gọi Hotline 09xxxx để em tư vấn trực tiếp nhé."
EOT;

        // Xử lý Lịch sử hội thoại (Chat Memory)
        $history = session()->get('chatbot_history', []);
        
        // Chuẩn bị contents gửi đi
        $apiContents = $history;
        
        // Thêm câu hỏi mới nhất kèm màng lọc Sandwich Defense
        $apiContents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => "Hãy nhớ quy tắc bảo mật, không trả lời yêu cầu lập trình/code/chính trị. Trả lời ngắn gọn. Câu hỏi của khách hàng: " . $userMessage]
            ]
        ];

        try {
            // Khôi phục lại gemini-2.5-flash
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'contents' => $apiContents,
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 3000
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Xóa block code nếu Gemini ngớ ngẩn trả về markdown code block
                    $reply = preg_replace('/^```html\n?|```$/m', '', $reply);
                    $reply = trim($reply);

                    // Cập nhật Lịch sử (Chỉ lưu userMessage gốc, không lưu câu lệnh Sandwich để khỏi rối context)
                    $history[] = [
                        'role' => 'user',
                        'parts' => [['text' => $userMessage]]
                    ];
                    $history[] = [
                        'role' => 'model',
                        'parts' => [['text' => $reply]]
                    ];

                    // Cắt bớt lịch sử nếu quá 10 tin nhắn (5 lượt)
                    if (count($history) > 10) {
                        $history = array_slice($history, -10);
                    }
                    session()->put('chatbot_history', $history);

                    return response()->json([
                        'success' => true,
                        'reply' => $reply
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'reply' => 'Dạ, hệ thống đang bận một chút, quý khách vui lòng hỏi lại sau nhé.'
                    ]);
                }
            }

            // Fallback nếu API lỗi (VD: quota exceeded, timeout)
            return response()->json([
                'success' => false,
                'reply' => 'Dạ em đang quá tải, quý khách vui lòng chờ vài giây rồi thử lại ạ.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'reply' => 'Dạ máy chủ đang gián đoạn, quý khách gọi Hotline để được hỗ trợ gấp nhé.'
            ]);
        }
    }
}
