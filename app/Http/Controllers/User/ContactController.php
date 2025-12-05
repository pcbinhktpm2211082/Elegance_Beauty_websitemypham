<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        $support = null;
        $messages = collect();

        if (Auth::check()) {
            $user = Auth::user();

            $support = Support::firstOrCreate(
                ['created_by' => $user->id],
                [
                    'name' => $user->name ?? 'Khách hàng',
                    'email' => $user->email ?? 'unknown@example.com',
                    'title' => 'Trao đổi với ' . ($user->name ?? 'khách hàng'),
                    'message' => 'Khách hàng ' . ($user->name ?? 'không xác định') . ' đã bắt đầu cuộc trò chuyện.',
                    'status' => 'pending',
                ]
            );

            $support->load(['messages' => function ($query) {
                $query->orderBy('created_at');
            }, 'messages.sender']);

            $messages = $support->messages;
        }

        return view('user.contact.index', compact('support', 'messages'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi tin nhắn.');
        }

        $request->validate([
            'message' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|max:4096',
        ], [
            'message.max' => 'Tin nhắn không được vượt quá 2000 ký tự',
            'attachments.*.image' => 'Tệp tải lên phải là hình ảnh',
            'attachments.*.max' => 'Ảnh không được vượt quá 4MB',
        ]);

        if (!$request->filled('message') && !$request->hasFile('attachments')) {
            return redirect()->back()->with('error', 'Vui lòng nhập nội dung hoặc chọn hình ảnh.');
        }

        try {
            $user = Auth::user();

            $support = Support::firstOrCreate(
                ['created_by' => $user->id],
                [
                    'name' => $user->name ?? 'Khách hàng',
                    'email' => $user->email ?? 'unknown@example.com',
                    'title' => 'Trao đổi với ' . ($user->name ?? 'khách hàng'),
                    'message' => 'Khách hàng ' . ($user->name ?? 'không xác định') . ' đã bắt đầu cuộc trò chuyện.',
                'status' => 'pending',
                ]
            );

            $createdMessages = [];

            if ($request->filled('message')) {
                $msg = $support->messages()->create([
                    'sender_id' => $user->id,
                    'is_admin' => false,
                    'message' => $request->input('message', ''),
                    'attachment_path' => null,
                ]);
                $createdMessages[] = $msg;
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPath = $file->store('support_attachments', 'public');
                    $msg = $support->messages()->create([
                        'sender_id' => $user->id,
                        'is_admin' => false,
                        'message' => null,
                        'attachment_path' => $attachmentPath,
                    ]);
                    $createdMessages[] = $msg;
                }
            }

            $support->update([
                'message' => $request->message ?: 'Đã gửi hình ảnh',
                'status' => $support->status === 'completed' ? 'completed' : 'pending',
            ]);

            AdminNotification::create([
                'title' => 'Yêu cầu hỗ trợ mới',
                'message' => 'Khách hàng ' . ($support->name ?? 'không xác định') . ' vừa gửi tin: "' . Str::limit($request->message ?? 'Đính kèm hình ảnh', 80) . '"',
                'type' => 'warning',
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'messages' => collect($createdMessages)->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'text' => $m->message,
                            'is_admin' => (bool) $m->is_admin,
                            'attachment_url' => $m->attachment_path ? Storage::url($m->attachment_path) : null,
                            'created_at_human' => $m->created_at?->diffForHumans() ?? '',
                        ];
                    })->values(),
                ]);
            }

            return redirect()->back()->with('success', 'Đã gửi tin nhắn! Admin sẽ phản hồi sớm nhất có thể.');
        } catch (\Exception $e) {
            Log::error('Contact chat error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
        }
    }

    public function aiMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để sử dụng chat AI.'], 401);
        }

        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ], [
            'message.required' => 'Vui lòng nhập nội dung.',
            'message.max' => 'Tin nhắn không được vượt quá 1000 ký tự.',
        ]);

        $userMessage = trim($data['message']);
        $reply = $this->buildAiReply($userMessage);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    protected function buildAiReply(string $message): string
    {
        $lower = mb_strtolower($message, 'UTF-8');

        // --- THÔNG TIN CHUNG VỀ SHOP / LIÊN HỆ / GIỜ LÀM VIỆC ---
        if (str_contains($lower, 'giờ làm việc') || str_contains($lower, 'mở cửa') || str_contains($lower, 'làm việc lúc nào')) {
            return '🤖 Giờ làm việc của shop: Thứ 2 - Thứ 7: 8:00 - 20:00, Chủ nhật: 9:00 - 18:00. '
                 . 'Bạn có thể đặt hàng online 24/7, đội hỗ trợ sẽ trả lời trong giờ làm việc.';
        }

        if (str_contains($lower, 'liên hệ') || str_contains($lower, 'số điện thoại') || str_contains($lower, 'email liên hệ')) {
            return '🤖 Bạn có thể liên hệ shop qua: '
                 . '• Điện thoại: +84 --------------------- '
                 . '• Email: info@elegancebeauty.com '
                 . 'Hoặc sử dụng khung chat này để được hỗ trợ nhanh nhất trong giờ làm việc.';
        }

        if (str_contains($lower, 'địa chỉ shop') || str_contains($lower, 'đến cửa hàng') || str_contains($lower, 'showroom')) {
            return '🤖 Địa chỉ cửa hàng được hiển thị trong phần Liên hệ trên website. '
                 . 'Hiện tại web chủ yếu hỗ trợ đặt hàng online và giao hàng tận nơi, bạn có thể đặt hàng tại đây để tiện lợi hơn.';
        }

        // --- TÀI KHOẢN / ĐĂNG NHẬP ---
        if (str_contains($lower, 'đăng nhập') || str_contains($lower, 'login') || str_contains($lower, 'không vào được tài khoản')) {
            return '🤖 Nếu bạn không đăng nhập được: hãy kiểm tra lại email/mật khẩu, thử bấm "Quên mật khẩu" để đặt lại, '
                 . 'và đảm bảo gõ đúng chữ hoa/thường. Nếu vẫn không được, bạn cho mình biết lỗi hiển thị trên màn hình nhé.';
        }

        if (str_contains($lower, 'quên mật khẩu') || str_contains($lower, 'đổi mật khẩu') || str_contains($lower, 'reset mật khẩu')) {
            return '🤖 Để đổi hoặc lấy lại mật khẩu, bạn bấm vào nút "Quên mật khẩu" trên màn hình đăng nhập, '
                 . 'nhập email đã đăng ký và làm theo hướng dẫn trong email gửi về. Sau khi đặt mật khẩu mới xong, hãy thử đăng nhập lại.';
        }

        if (str_contains($lower, 'đăng ký') || str_contains($lower, 'tạo tài khoản') || str_contains($lower, 'register')) {
            return '🤖 Để tạo tài khoản mới, bạn vào mục Đăng ký trên thanh menu, nhập họ tên, email, mật khẩu rồi bấm xác nhận. '
                 . 'Sau khi đăng ký thành công, bạn có thể theo dõi đơn hàng và lưu thông tin giao hàng cho các lần mua sau.';
        }

        // --- ĐƠN HÀNG / QUY TRÌNH MUA HÀNG ---
        if (str_contains($lower, 'đơn hàng') || str_contains($lower, 'order') || str_contains($lower, 'mã đơn')) {
            return '🤖 Mình có thể giúp bạn giải thích trạng thái đơn, thời gian giao hàng dự kiến và các bước xử lý nếu có vấn đề. '
                 . 'Bạn cho mình biết mã đơn (hoặc mô tả thời gian đặt, sản phẩm trong đơn) và tình trạng bạn đang gặp phải nhé.';
        }

        if (str_contains($lower, 'cách mua') || str_contains($lower, 'đặt hàng như thế nào') || str_contains($lower, 'hướng dẫn mua hàng')) {
            return '🤖 Quy trình đặt hàng: '
                 . '1) Chọn sản phẩm và số lượng, bấm "Thêm vào giỏ". '
                 . '2) Vào Giỏ hàng để kiểm tra lại. '
                 . '3) Bấm "Thanh toán", nhập thông tin giao hàng, chọn phương thức thanh toán. '
                 . '4) Xác nhận đơn hàng. Bạn có thể theo dõi đơn tại mục "Đơn hàng của tôi".';
        }

        if (str_contains($lower, 'hủy đơn') || str_contains($lower, 'huỷ đơn') || str_contains($lower, 'cancel order')) {
            return '🤖 Bạn có thể hủy đơn khi đơn còn ở trạng thái "Chờ xử lý". '
                 . 'Vào "Đơn hàng của tôi" → chọn đơn cần hủy → nếu còn nút Hủy thì bấm để thực hiện. '
                 . 'Nếu đơn đã chuyển sang "Đang xử lý" hoặc "Đang giao", bạn nhắn cho Admin để được hỗ trợ thêm.';
        }

        if (str_contains($lower, 'đổi địa chỉ') || str_contains($lower, 'sai địa chỉ')) {
            return '🤖 Nếu bạn vừa đặt đơn xong và nhập sai địa chỉ, hãy nhắn ngay cho Admin (tab Chat với Admin) kèm mã đơn và địa chỉ đúng. '
                 . 'Khi đơn chưa giao cho đơn vị vận chuyển, shop vẫn có thể hỗ trợ cập nhật địa chỉ cho bạn.';
        }

        // --- GIAO HÀNG / VẬN CHUYỂN ---
        if (str_contains($lower, 'giao hàng') || str_contains($lower, 'ship') || str_contains($lower, 'vận chuyển')) {
            return '🤖 Thời gian giao hàng thường từ 2–5 ngày làm việc tuỳ khu vực. '
                 . 'Bạn đang gặp vấn đề gì với việc giao hàng (giao chậm, thất lạc, sai địa chỉ...)? Mình sẽ gợi ý bước xử lý phù hợp cho bạn.';
        }

        if (str_contains($lower, 'phí ship') || str_contains($lower, 'phí giao') || str_contains($lower, 'bao ship')) {
            return '🤖 Phí vận chuyển thường được tính dựa trên địa chỉ nhận hàng và đơn vị vận chuyển. '
                 . 'Bạn có thể xem chính xác phí ship ở bước Thanh toán sau khi nhập địa chỉ. Một số chương trình khuyến mãi có thể miễn/giảm phí ship theo điều kiện giá trị đơn.';
        }

        // --- THANH TOÁN ---
        if (str_contains($lower, 'thanh toán') || str_contains($lower, 'chuyển khoản') || str_contains($lower, 'payment')) {
            return '🤖 Hiện shop hỗ trợ nhiều phương thức thanh toán: tiền mặt khi nhận hàng (COD) và các phương thức online. '
                 . 'Bạn mô tả giúp mình lỗi thanh toán (ví dụ: không hiện trang thanh toán, ngân hàng báo lỗi...) để mình hướng dẫn các bước khắc phục cơ bản nhé.';
        }

        if (str_contains($lower, 'cod') || str_contains($lower, 'tiền mặt') || str_contains($lower, 'khi nhận hàng')) {
            return '🤖 Bạn có thể chọn thanh toán khi nhận hàng (COD) ở bước chọn phương thức thanh toán. '
                 . 'Nhân viên giao hàng sẽ thu tiền mặt đúng số tiền trên đơn khi giao sản phẩm cho bạn.';
        }

        // --- VOUCHER / MÃ GIẢM GIÁ ---
        if (str_contains($lower, 'mã giảm giá') || str_contains($lower, 'voucher') || str_contains($lower, 'khuyến mãi')) {
            return '🤖 Để sử dụng mã giảm giá, bạn nhập mã vào ô Voucher ở bước Thanh toán, sau đó bấm Áp dụng. '
                 . 'Một số mã có điều kiện về giá trị đơn tối thiểu, thời gian sử dụng hoặc giới hạn số lần, bạn nhớ kiểm tra thông tin chi tiết của mã nhé.';
        }

        if (str_contains($lower, 'mã không dùng được') || str_contains($lower, 'voucher lỗi') || str_contains($lower, 'không áp dụng được mã')) {
            return '🤖 Nếu mã giảm giá không dùng được, thường là do: mã đã hết hạn, đã dùng đủ số lần, không đủ giá trị đơn tối thiểu, '
                 . 'hoặc không áp dụng cho sản phẩm trong giỏ. Bạn kiểm tra lại điều kiện mã, nếu vẫn không được thì chụp màn hình lỗi gửi cho Admin nhé.';
        }

        // --- ĐỔI TRẢ / HOÀN TIỀN / BẢO HÀNH ---
        if (str_contains($lower, 'đổi trả') || str_contains($lower, 'hoàn tiền') || str_contains($lower, 'bảo hành')) {
            return '🤖 Chính sách đổi trả: thông thường hỗ trợ đổi trả trong 3–7 ngày nếu sản phẩm lỗi, hư hỏng hoặc giao sai. '
                 . 'Bạn chia sẻ giúp mình tình trạng sản phẩm (lỗi gì, nhận hàng khi nào, còn tem/mã đơn không) để mình gợi ý hướng xử lý phù hợp nhé.';
        }

        // --- TƯ VẤN DA / SẢN PHẨM ---
        // Đặt các case cụ thể (da dầu/khô/mụn/nhạy cảm) LÊN TRƯỚC, để khi trong câu có cả "tư vấn" và loại da
        // thì sẽ trả lời theo từng loại da thay vì câu chung chung.
        if (str_contains($lower, 'da dầu') || str_contains($lower, 'da nhờn')) {
            return '🤖 Với da dầu/da nhờn, bạn nên ưu tiên: '
                 . '• Sữa rửa mặt dịu nhẹ, kiểm soát dầu, không làm khô căng da. '
                 . '• Toner cân bằng, không cồn hoặc cồn thấp. '
                 . '• Serum chứa BHA/Niacinamide giúp giảm dầu và thu nhỏ lỗ chân lông (dùng từ từ 2–3 lần/tuần nếu mới bắt đầu). '
                 . '• Kem dưỡng dạng gel hoặc lotion mỏng nhẹ, không gây bí da. '
                 . '• Kem chống nắng dạng gel/lỏng, oil-free (ban ngày). '
                 . 'Nếu bạn mô tả rõ hơn tình trạng mụn/thâm, mình có thể gợi ý kỹ hơn.';
        }

        if (str_contains($lower, 'da khô') || str_contains($lower, 'khô da') || str_contains($lower, 'thiếu ẩm')) {
            return '🤖 Với da khô/thiếu ẩm, bạn nên tập trung: '
                 . '• Sữa rửa mặt dịu nhẹ, không tạo bọt quá nhiều, không chứa chất tẩy rửa mạnh. '
                 . '• Toner/essence dưỡng ẩm, có Hyaluronic Acid, Glycerin, Panthenol... '
                 . '• Serum cấp ẩm sâu (HA, Ceramide, Peptide). '
                 . '• Kem dưỡng đặc hơn một chút để khoá ẩm, nhất là buổi tối. '
                 . '• Ban ngày vẫn cần kem chống nắng để bảo vệ da. '
                 . 'Bạn có thể cho biết thêm da bạn có bong tróc, căng rát hay không để mình gợi ý kỹ hơn.';
        }

        if (str_contains($lower, 'da mụn') || str_contains($lower, 'mụn nhiều') || str_contains($lower, 'mụn viêm') || str_contains($lower, 'mụn ẩn')) {
            return '🤖 Với da mụn, quan trọng nhất là: làm sạch dịu nhẹ, không chà xát mạnh và dùng hoạt chất phù hợp. '
                 . 'Routine gợi ý: '
                 . '• Tối: Tẩy trang (nếu có makeup/kem chống nắng) → Rửa mặt dịu nhẹ → Toner cân bằng → Serum trị mụn (BHA/AHA/Niacinamide tuỳ loại mụn) → Kem dưỡng phục hồi. '
                 . '• Sáng: Rửa mặt nhẹ → Toner → Kem dưỡng nhẹ → Kem chống nắng. '
                 . 'Không nên tự nặn mụn tại nhà, hạn chế dùng quá nhiều sản phẩm mới cùng lúc. Bạn mô tả loại mụn (đầu đen, đầu trắng, mụn viêm...) để mình tư vấn kỹ hơn.';
        }

        if (str_contains($lower, 'da nhạy cảm') || str_contains($lower, 'dễ kích ứng') || str_contains($lower, 'kích ứng da')) {
            return '🤖 Da nhạy cảm cần ưu tiên tối giản routine và chọn sản phẩm dịu nhẹ: '
                 . '• Rửa mặt với sữa rửa mặt pH cân bằng, không hương liệu/cồn. '
                 . '• Tránh tẩy tế bào chết mạnh, tránh dùng quá nhiều hoạt chất cùng lúc. '
                 . '• Dùng kem dưỡng phục hồi hàng rào bảo vệ da (chứa Ceramide, Centella, Panthenol...). '
                 . '• Luôn dùng kem chống nắng phổ rộng, dịu nhẹ cho da nhạy cảm. '
                 . 'Nếu bạn cho mình biết sản phẩm nào từng làm da bạn đỏ/rát, mình sẽ tư vấn cách tránh và thay thế.';
        }

        if (str_contains($lower, 'tư vấn') || str_contains($lower, 'chăm sóc da') || str_contains($lower, 'routine')) {
            return '🤖 Để tư vấn da chính xác, bạn giúp mình trả lời 3 ý: '
                 . '1) Loại da: dầu / khô / hỗn hợp / nhạy cảm. '
                 . '2) Tình trạng hiện tại: mụn, thâm, nám, lỗ chân lông to, da xỉn màu, nhiều dầu vùng T-zone... '
                 . '3) Mục tiêu: giảm mụn, giảm thâm, dưỡng ẩm, sáng da, chống lão hoá... '
                 . 'Sau đó mình sẽ gợi ý routine cơ bản và nhóm sản phẩm phù hợp cho bạn.';
        }

        if (str_contains($lower, 'cách dùng') || str_contains($lower, 'sử dụng như thế nào') || str_contains($lower, 'dùng như thế nào')) {
            return '🤖 Với mỗi sản phẩm, bạn nên xem hướng dẫn sử dụng chi tiết trong phần mô tả. '
                 . 'Thông thường: tẩy trang → rửa mặt → toner → serum → kem dưỡng → kem chống nắng (ban ngày). '
                 . 'Nếu bạn nói rõ tên sản phẩm, mình có thể gợi ý cách dùng cơ bản cho bạn.';
        }

        if (str_contains($lower, 'thành phần') || str_contains($lower, 'ingredient') || str_contains($lower, 'an toàn') || str_contains($lower, 'dị ứng')) {
            return '🤖 Thành phần sản phẩm thường được liệt kê trong phần mô tả hoặc trên bao bì. '
                 . 'Nếu bạn có tiền sử dị ứng với một số hoạt chất (ví dụ: hương liệu, cồn, paraben...), hãy cho mình biết để mình gợi ý nhóm sản phẩm an toàn hơn. '
                 . 'Khi dùng sản phẩm mới, bạn nên test thử ở một vùng da nhỏ trước.';
        }

        // --- SẢN PHẨM HẾT HÀNG / CÒN HÀNG ---
        if (str_contains($lower, 'hết hàng') || str_contains($lower, 'còn hàng không') || str_contains($lower, 'khi nào có hàng lại')) {
            return '🤖 Tình trạng còn hàng/hết hàng của từng sản phẩm được hiển thị ngay trên trang chi tiết sản phẩm. '
                 . 'Nếu sản phẩm đang hết hàng, bạn có thể theo dõi lại sau hoặc hỏi Admin để được gợi ý sản phẩm tương tự.';
        }

        // --- BẢO MẬT THÔNG TIN / TÀI KHOẢN ---
        if (str_contains($lower, 'bảo mật') || str_contains($lower, 'an toàn thông tin') || str_contains($lower, 'lộ thông tin')) {
            return '🤖 Thông tin tài khoản và đơn hàng của bạn được lưu trữ trên hệ thống bảo mật của shop. '
                 . 'Bạn nên giữ bí mật mật khẩu, không chia sẻ cho người khác và luôn đăng xuất ở máy lạ. '
                 . 'Nếu nghi ngờ tài khoản bị truy cập trái phép, hãy đổi mật khẩu ngay và báo cho Admin.';
        }

        // --- TRỢ GIÚP CHUNG / NĂNG LỰC CỦA AI ---
        if (str_contains($lower, 'bạn có thể giúp') || str_contains($lower, 'bạn giúp được gì') || str_contains($lower, 'giúp mình những gì')) {
            return '🤖 Mình có thể giúp bạn: giải thích trạng thái đơn hàng, tư vấn giao hàng & thanh toán, gợi ý sản phẩm và routine chăm sóc da, '
                 . 'và hướng dẫn các bước cơ bản nếu bạn gặp sự cố. Bạn đang quan tâm đến vấn đề nào để mình hỗ trợ ngay cho bạn?';
        }

        return '🤖 Mình là trợ lý ảo của cửa hàng, mình có thể hỗ trợ bạn về: đơn hàng, giao hàng, thanh toán, đổi trả và tư vấn chọn sản phẩm. '
             . 'Bạn mô tả ngắn gọn vấn đề hoặc nhu cầu của bạn (ví dụ: "tư vấn da dầu mụn", "hỏi về đơn hàng #123", "lỗi thanh toán") để mình hỗ trợ chi tiết hơn nhé.';
    }
}
