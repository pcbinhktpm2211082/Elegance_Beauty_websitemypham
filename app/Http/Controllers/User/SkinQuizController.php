<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkinQuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('user.profile.skin-quiz');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'q1' => 'required|in:A,B,C,D',
            'q2' => 'required|in:A,B,C,D',
            'q3' => 'required|in:A,B',
            'q4' => 'required|array',
            'q4.*' => 'in:A,B,C,D',
            'q5' => 'required|in:A,B',
        ]);

        $user = Auth::user();
        
        // Tính điểm cho từng loại da
        $scores = [
            'dry' => 0,
            'oily' => 0,
            'combination' => 0,
            'normal' => 0,
        ];

        // Q1: Sau khi rửa mặt
        switch ($request->q1) {
            case 'A': // Rất căng, khô rát
                $scores['dry'] += 3;
                break;
            case 'B': // Hơi khô ở hai bên má
                $scores['combination'] += 1;
                break;
            case 'C': // Mềm mại, dễ chịu
                $scores['normal'] += 1;
                break;
            case 'D': // Bóng dầu nhẹ ở vùng chữ T
                $scores['oily'] += 2;
                break;
        }

        // Q2: Cuối ngày vùng chữ T
        switch ($request->q2) {
            case 'A': // Không hề, da vẫn lì và mờ
                $scores['dry'] += 3;
                break;
            case 'B': // Hơi bóng dầu nhẹ ở mũi
                $scores['normal'] += 2;
                break;
            case 'C': // Bóng dầu rõ rệt
                $scores['oily'] += 3;
                break;
            case 'D': // Vùng khô (má) và vùng dầu (chữ T) rõ rệt
                $scores['combination'] += 3;
                break;
        }

        // Q3: Lỗ chân lông
        switch ($request->q3) {
            case 'A': // Lỗ chân lông nhỏ
                $scores['normal'] += 1;
                $scores['dry'] += 1;
                break;
            case 'B': // Lỗ chân lông to thấy rõ
                $scores['oily'] += 3;
                break;
        }

        // QUY TẮC ƯU TIÊN 1: Da Hỗn Hợp (Kiểm tra trước)
        // Nếu có Q1-B (Hơi khô/căng nhẹ ở má) VÀ Q2-D (Có vùng khô và vùng dầu rõ rệt)
        // thì ưu tiên Da Hỗn Hợp
        if ($request->q1 === 'B' && $request->q2 === 'D') {
            $skinType = 'combination';
        }
        // QUY TẮC ƯU TIÊN 2: Da Dầu
        // Nếu có Q2-C (Bóng dầu rõ rệt) VÀ Q3-B (Lỗ chân lông to thấy rõ)
        // VÀ KHÔNG có Q1-A (Rất căng, khô rát) hoặc Q1-B (Hơi khô ở má)
        // thì ưu tiên Da Dầu
        elseif ($request->q2 === 'C' && $request->q3 === 'B' && $request->q1 !== 'A' && $request->q1 !== 'B') {
            $skinType = 'oily';
        }
        // Nếu không thỏa mãn quy tắc ưu tiên, xác định loại da có điểm cao nhất
        else {
            $maxScore = max($scores);
            $skinType = array_search($maxScore, $scores);
            
            // Nếu điểm cân bằng ở mức thấp, mặc định là Normal
            if ($maxScore <= 2) {
                $skinType = 'normal';
            }
        }

        // Map sang giá trị trong database
        $skinTypeMap = [
            'dry' => 'dry',
            'oily' => 'oily',
            'combination' => 'combination',
            'normal' => 'normal',
        ];
        
        $finalSkinType = $skinTypeMap[$skinType] ?? 'normal';

        // Xử lý Q4: Vấn đề da (có thể chọn nhiều)
        $skinConcerns = [];
        foreach ($request->q4 as $answer) {
            switch ($answer) {
                case 'A': // Mụn
                    $skinConcerns[] = 'acne';
                    break;
                case 'B': // Lão hóa
                    $skinConcerns[] = 'anti-aging';
                    break;
                case 'C': // Tăng sắc tố
                    $skinConcerns[] = 'brightening';
                    break;
                case 'D': // Mất nước/Thiếu ẩm
                    $skinConcerns[] = 'hydration';
                    break;
            }
        }

        // Q5: Da nhạy cảm
        $isSensitive = $request->q5 === 'A';

        // Cập nhật thông tin người dùng
        $user->update([
            'skin_type' => $finalSkinType,
            'skin_concerns' => $skinConcerns,
            'is_sensitive' => $isSensitive,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Kết quả quiz đã được lưu! Loại da của bạn: ' . $user->skin_type_text);
    }
}
