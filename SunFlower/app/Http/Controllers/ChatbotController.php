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

        // Cơ chế API Key Rotation
        $apiKeysStr = env('GEMINI_API_KEYS', config('services.gemini.key'));
        $apiKeys = array_filter(array_map('trim', explode(',', $apiKeysStr)));
        
        if (empty($apiKeys)) {
            return response()->json(['error' => 'Hệ thống đang bảo trì. Vui lòng thử lại sau.'], 500);
        }
        $apiKey = $apiKeys[array_rand($apiKeys)];

        // ==========================================
        // 2. CUNG CẤP TOÀN BỘ DANH MỤC SẢN PHẨM (OMNISCIENCE)
        // ==========================================
        // Do Gemini có Context Window rất lớn (1M tokens), thay vì lọc từ khóa thủ công (Regex hên xui),
        // ta nhồi toàn bộ danh sách sản phẩm (tối đa 100) vào "não" của AI để AI tự do phân tích và trả lời.
        $products = \App\Models\SanPham::select('masp', 'tensp', 'giaban')
                                       ->orderBy('masp', 'desc')
                                       ->limit(100)
                                       ->get();

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
            return response()->stream(function () use ($apiKey, $systemInstruction, $apiContents, $userMessage) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent?alt=sse&key={$apiKey}";
                
                $payload = [
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
                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                // Biến lưu trữ lịch sử phản hồi để save vào session
                $fullReply = '';

                // Streaming callback
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$fullReply) {
                    // Phân tích SSE chunk để lấy text lưu lịch sử
                    $lines = explode("\n", $data);
                    foreach ($lines as $line) {
                        if (strpos($line, 'data: ') === 0) {
                            $jsonStr = substr($line, 6);
                            $json = json_decode($jsonStr, true);
                            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                                $fullReply .= $json['candidates'][0]['content']['parts'][0]['text'];
                            }
                        }
                    }

                    // Flush trực tiếp ra trình duyệt
                    echo $data;
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                    return strlen($data);
                });

                curl_exec($ch);
                curl_close($ch);

                // Cập nhật Lịch sử (Chat Memory) thủ công
                $history = session()->get('chatbot_history', []);
                $history[] = [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]]
                ];
                $history[] = [
                    'role' => 'model',
                    'parts' => [['text' => trim($fullReply)]]
                ];
                if (count($history) > 10) {
                    $history = array_slice($history, -10);
                }
                session()->put('chatbot_history', $history);
                session()->save(); // Bắt buộc gọi save() vì đang trong stream response

            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'reply' => 'Dạ máy chủ đang gián đoạn, quý khách gọi Hotline để được hỗ trợ gấp nhé.'
            ]);
        }
    }
}
