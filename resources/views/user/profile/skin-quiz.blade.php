@extends('layouts.user')

@section('title', 'Quiz Phân Loại Da')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>📝 Quiz Phân Loại Da</h1>
        <p>Trả lời các câu hỏi để xác định loại da và vấn đề da của bạn</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-container">
        <div class="profile-section">
            <form method="POST" action="{{ route('skin-quiz.submit') }}" class="profile-form">
                @csrf
                
                <!-- Phần 1: Phân Loại Da Cơ Bản -->
                <div class="quiz-section">
                    <h2 class="section-title">Phần 1: Phân Loại Da Cơ Bản</h2>
                    <p class="section-description">Xác định Dầu/Khô/Hỗn hợp/Thường</p>

                    <!-- Q1 -->
                    <div class="quiz-question">
                        <h3 class="question-title">Q1: Sau khi rửa mặt bằng sữa rửa mặt dịu nhẹ (30 phút sau), bạn cảm thấy da mình thế nào?</h3>
                        <div class="quiz-options">
                            <label class="quiz-option">
                                <input type="radio" name="q1" value="A" required>
                                <span>A. Rất căng, khô rát, dễ bong tróc.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q1" value="B" required>
                                <span>B. Hơi khô hoặc căng nhẹ ở hai bên má.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q1" value="C" required>
                                <span>C. Cảm giác mềm mại, dễ chịu.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q1" value="D" required>
                                <span>D. Đã bắt đầu thấy bóng dầu nhẹ ở vùng chữ T.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="quiz-question">
                        <h3 class="question-title">Q2: Vào cuối ngày (khoảng 6-8 tiếng sau), bạn thấy vùng chữ T (trán, mũi, cằm) có bóng dầu nhiều không?</h3>
                        <div class="quiz-options">
                            <label class="quiz-option">
                                <input type="radio" name="q2" value="A" required>
                                <span>A. Không hề, da vẫn lì và mờ.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q2" value="B" required>
                                <span>B. Hơi bóng dầu nhẹ ở mũi.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q2" value="C" required>
                                <span>C. Bóng dầu rõ rệt, đặc biệt là ở mũi và trán.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q2" value="D" required>
                                <span>D. Da có vùng khô (má) và vùng dầu (chữ T) rõ rệt.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="quiz-question">
                        <h3 class="question-title">Q3: Bạn có thường xuyên thấy lỗ chân lông bị giãn nở rõ rệt ở mũi và má trong không?</h3>
                        <div class="quiz-options">
                            <label class="quiz-option">
                                <input type="radio" name="q3" value="A" required>
                                <span>A. Không, lỗ chân lông nhỏ.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q3" value="B" required>
                                <span>B. Có, lỗ chân lông to thấy rõ.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Phần 2: Xác định Vấn đề Da và Độ Nhạy cảm -->
                <div class="quiz-section">
                    <h2 class="section-title">Phần 2: Xác định Vấn đề Da và Độ Nhạy cảm</h2>

                    <!-- Q4 -->
                    <div class="quiz-question">
                        <h3 class="question-title">Q4: Vấn đề nào bạn muốn giải quyết nhất hiện tại? (Có thể chọn nhiều)</h3>
                        <div class="quiz-options">
                            <label class="quiz-option">
                                <input type="checkbox" name="q4[]" value="A">
                                <span>A. Mụn (Mụn viêm, mụn đầu đen, mụn ẩn).</span>
                            </label>
                            <label class="quiz-option">
                                <input type="checkbox" name="q4[]" value="B">
                                <span>B. Nếp nhăn, da mất đàn hồi, chảy xệ (Lão hóa).</span>
                            </label>
                            <label class="quiz-option">
                                <input type="checkbox" name="q4[]" value="C">
                                <span>C. Thâm mụn, nám, tàn nhang, da xỉn màu.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="checkbox" name="q4[]" value="D">
                                <span>D. Da khô căng, thiếu nước.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div class="quiz-question">
                        <h3 class="question-title">Q5: Da bạn có thường xuyên bị mẩn đỏ, ngứa, hoặc châm chích khi dùng sản phẩm mới không?</h3>
                        <div class="quiz-options">
                            <label class="quiz-option">
                                <input type="radio" name="q5" value="A" required>
                                <span>A. Có, da dễ bị kích ứng.</span>
                            </label>
                            <label class="quiz-option">
                                <input type="radio" name="q5" value="B" required>
                                <span>B. Bình thường, hiếm khi bị kích ứng.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check"></i>
                        Hoàn thành Quiz
                    </button>
                    
                    <a href="{{ route('profile.edit') }}" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.quiz-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.section-description {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.quiz-question {
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 6px;
    border-left: 4px solid #3b82f6;
}

.question-title {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
}

.quiz-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quiz-option {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.quiz-option:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.quiz-option input[type="radio"],
.quiz-option input[type="checkbox"] {
    margin-top: 0.25rem;
    cursor: pointer;
}

.quiz-option input[type="radio"]:checked + span,
.quiz-option input[type="checkbox"]:checked + span {
    font-weight: 600;
    color: #3b82f6;
}

.quiz-option span {
    flex: 1;
    color: #374151;
    line-height: 1.5;
}
</style>
@endsection

