@extends('layouts.user')

@section('title', 'Liên hệ')

@push('styles')
<style>
    .chat-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eceff5;
        box-shadow: 0 15px 25px rgba(15, 23, 42, 0.08);
        height: 100%;
        padding: 24px;
    }
    .contact-header {
        margin-bottom: 18px;
    }
    .contact-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827 !important; /* giống màu tiêu đề Tất cả sản phẩm */
        margin-bottom: 4px;
    }
    .contact-subtitle {
        font-size: 13px;
        color: #6b7280;
    }
    .chat-window {
        flex: 1;
        border: 1px solid #edf2f7;
        border-radius: 16px;
        padding: 16px;
        background: #f8fafc;
        overflow-y: auto;
        max-height: 640px;
        margin-bottom: 16px;
    }
    .chat-row {
        display: flex;
        margin-bottom: 12px;
    }
    .chat-row.user { justify-content: flex-end; }
    .chat-row.admin,
    .chat-row.ai { justify-content: flex-start; }
    .chat-bubble {
        max-width: 75%;
        border-radius: 18px;
        padding: 14px 18px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    /* Tin nhắn người dùng – tông tím theo giao diện admin */
    .chat-row.user .chat-bubble {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        border-color: transparent;
    }
    /* Tin nhắn admin & AI – cùng tone xanh nhạt cho câu trả lời từ hệ thống */
    .chat-row.admin .chat-bubble,
    .chat-row.ai .chat-bubble {
        background: #ecfeff;
        border-color: #bae6fd;
        color: #0f172a;
    }
    .chat-meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-top: 8px;
        opacity: .8;
    }
    .chat-form textarea {
        width: 100%;
        border-radius: 16px;
        border: 1px solid #d7dce5;
        padding: 12px 16px;
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }
    .chat-actions {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        align-items: center;
    }
    .chat-actions button[type="submit"] {
        flex: 1;
        border: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #b45309, #92400e); /* nâu cam đồng bộ giao diện */
        color: #fff;
        padding: 12px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        cursor: pointer;
    }
    .chat-actions button.image-btn {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 50%;
        border: none;
        background: #fef3c7; /* nền vàng nhạt pha nâu */
        color: #b45309;      /* icon nâu cam đồng bộ với nút gửi */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .chat-actions button.image-btn:hover { background: #fde68a; }
    .chat-empty {
        text-align: center;
        color: #94a3b8;
        margin-top: 40px;
    }
    /* Điều chỉnh bố cục liên hệ/chat */
    .contact-container {
        display: flex;
        gap: 32px;
        align-items: flex-start;
    }

    /* Bố cục chat AI/Admin */
    .chat-layout {
        display: flex;
        gap: 16px;
        min-height: 420px;
    }
    .chat-sidebar {
        flex: 0 0 220px;
        border-radius: 14px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .chat-sidebar-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: 2px;
        padding: 0 4px;
    }
    .chat-conversation-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 10px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background .15s ease, border-color .15s ease, box-shadow .15s ease;
        font-size: 13px;
    }
    .chat-conversation-item .avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }
    .chat-conversation-item .info-title {
        font-weight: 600;
        color: #111827;
    }
    .chat-conversation-item .info-subtitle {
        font-size: 11px;
        color: #6b7280;
    }
    .chat-conversation-item.active {
        background: #eef2ff;
        border-color: #4f46e5;
        box-shadow: 0 0 0 1px rgba(79,70,229,0.15);
    }

    .chat-main {
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .chat-toggle {
        display: inline-flex;
        align-items: center;
        padding: 4px;
        border-radius: 999px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        margin-bottom: 10px;
    }
    .chat-toggle button {
        border: none;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 12px;
        cursor: pointer;
        background: transparent;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chat-toggle button.active {
        background: #ffffff;
        color: #111827;
        box-shadow: 0 1px 3px rgba(15,23,42,0.12);
    }

    .chat-avatar-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }
    .chat-avatar {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        background: #e5e7eb;
    }
    .chat-row.user .chat-avatar-wrapper {
        flex-direction: row-reverse;
    }
    .chat-row.user .chat-avatar {
        background: #c4b5fd;
        color: #1e1b4b;
    }
    .chat-row.admin .chat-avatar {
        background: #d1fae5;
        color: #065f46;
    }
    .chat-row.ai .chat-avatar {
        background: #e0f2fe;
        color: #0369a1;
    }

    @media (max-width: 768px) {
        .chat-layout {
            flex-direction: column;
        }
        .chat-sidebar {
            flex: 1 1 auto;
        }
    }
    .contact-info {
        flex: 0 0 30%;
        max-width: 30%;
    }
    .contact-form-container {
        flex: 1 1 0;
        max-width: 70%;
    }
    @media (max-width: 768px) {
        .contact-container {
            flex-direction: column;
        }
        .contact-info,
        .contact-form-container {
            flex: 1 1 auto;
            max-width: 100%;
        }
    }

    /* Gợi ý câu hỏi AI */
    .ai-suggestions {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .ai-suggestion-btn {
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 6px 12px;
        font-size: 11px;
        color: #4b5563;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
        white-space: nowrap;
    }
    .ai-suggestion-btn:hover {
        background: #fef3c7;
        border-color: #fbbf24;
        color: #92400e;
        box-shadow: 0 1px 3px rgba(15,23,42,0.15);
    }
</style>
@endpush

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<main class="contact-main">
    <div class="contact-header">
        <h1 class="contact-title">Hỗ trợ trực tiếp</h1>
        <p class="contact-subtitle">Nhận phản hồi nhanh chóng qua khung chat trực tiếp</p>
    </div>

    <div class="contact-container">
        <!-- Thông tin liên hệ -->
        <div class="contact-info">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Địa chỉ</h3>
                    <p>---------------------</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <h3>Điện thoại</h3>
                    <p>+84 ---------------------</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>info@elegancebeauty.com</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h3>Giờ làm việc</h3>
                    <p>Thứ 2 - Thứ 7: 8:00 - 20:00<br>Chủ nhật: 9:00 - 18:00</p>
                </div>
            </div>
        </div>

        <!-- Chat -->
        <div class="contact-form-container">
            <div class="chat-card">
                <div class="form-header" style="margin-bottom: 12px;">
                    <div>
                        <h2 style="font-size:18px; font-weight:600; color:#92400e;">Trò chuyện trực tiếp</h2>
                        <p style="font-size:13px; color:#6b7280;">Gửi tin nhắn và nhận phản hồi từ đội ngũ hỗ trợ</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @auth
                    <div class="chat-layout">
                        <!-- Sidebar: danh sách cuộc trò chuyện -->
                        <div class="chat-sidebar">
                            <div class="chat-sidebar-title">Cuộc trò chuyện</div>

                            <div class="chat-conversation-item active" data-chat-target="ai" onclick="switchChatMode('ai')">
                                <div class="avatar" style="background:#e0f2fe; color:#0369a1;">🤖</div>
                                <div>
                                    <div class="info-title">Trợ lý AI</div>
                                    <div class="info-subtitle">Tư vấn tự động 24/7</div>
                                </div>
                            </div>

                            <div class="chat-conversation-item" data-chat-target="admin" onclick="switchChatMode('admin')">
                                <div class="avatar" style="background:#d1fae5; color:#065f46;">👤</div>
                                <div>
                                    <div class="info-title">Chat với Admin</div>
                                    <div class="info-subtitle">Hỗ trợ viên trong giờ làm việc</div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel chính: nội dung chat -->
                        <div class="chat-main">
                            <div class="chat-toggle">
                                <button type="button" id="toggle-ai" class="active" onclick="switchChatMode('ai')">
                                    🤖 AI
                                </button>
                                <button type="button" id="toggle-admin" onclick="switchChatMode('admin')">
                                    👤 Admin
                                </button>
                            </div>

                            <!-- Panel chat AI -->
                            <div id="ai-chat-panel">
                                <div id="ai-chat-window" class="chat-window">
                                    <div class="chat-row ai">
                                        <div class="chat-avatar-wrapper">
                                            <div class="chat-avatar">🤖</div>
                                            <div class="chat-bubble">
                                                <p>Xin chào! Mình là trợ lý AI của cửa hàng. Bạn có thể hỏi về đơn hàng, giao hàng, thanh toán hoặc tư vấn chọn sản phẩm nhé.</p>
                                                <div class="chat-meta">
                                                    <span>AI</span>
                                                    <span>Vừa xong</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form id="ai-chat-form" class="chat-form" onsubmit="sendAiMessage(event)">
                                    <textarea id="ai-message-input" name="message" placeholder="Nhập câu hỏi cho AI..."></textarea>
                                    <div class="chat-actions">
                                        <button type="submit">
                                            <i class="fas fa-robot"></i>
                                            Gửi cho AI
                                        </button>
                                    </div>
                                    <div class="ai-suggestions">
                                        <button type="button" class="ai-suggestion-btn"
                                                onclick="fillAiSuggestion('Mình muốn hỏi về đơn hàng của mình.')">
                                            Hỏi về đơn hàng
                                        </button>
                                        <button type="button" class="ai-suggestion-btn"
                                                onclick="fillAiSuggestion('Da mình dầu và hay bị mụn, bạn tư vấn giúp mình routine phù hợp.')">
                                            Tư vấn da dầu mụn
                                        </button>
                                        <button type="button" class="ai-suggestion-btn"
                                                onclick="fillAiSuggestion('Cách dùng mã giảm giá và lý do mã giảm giá không áp dụng được?')">
                                            Dùng mã giảm giá
                                        </button>
                                        <button type="button" class="ai-suggestion-btn"
                                                onclick="fillAiSuggestion('Sản phẩm bị lỗi thì chính sách đổi trả và hoàn tiền như thế nào?')">
                                            Đổi trả / hoàn tiền
                                        </button>
                                        <button type="button" class="ai-suggestion-btn"
                                                onclick="fillAiSuggestion('Bạn có thể giúp mình những gì trên trang web này?')">
                                            Bạn giúp được gì?
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Panel chat Admin (giữ nguyên logic cũ) -->
                            <div id="admin-chat-panel" style="display:none;">
                                <div class="chat-window">
                                    @forelse($messages as $message)
                                        <div class="chat-row {{ $message->is_admin ? 'admin' : 'user' }}">
                                            <div class="chat-avatar-wrapper">
                                                <div class="chat-avatar">
                                                    {{ $message->is_admin ? '👤' : '🙂' }}
                                                </div>
                                                <div class="chat-bubble">
                                                    @if($message->message)
                                                        <p>{{ $message->message }}</p>
                                                    @endif
                                                    @if($message->attachment_path)
                                                        @php $imgUrl = Storage::url($message->attachment_path); @endphp
                                                        <div style="margin-top:10px;border-radius:12px;max-width:180px;overflow:hidden;cursor:pointer;box-shadow:0 4px 10px rgba(15,23,42,.18);"
                                                             onclick="event.stopPropagation(); openUserImageModal('{{ $imgUrl }}')">
                                                            <img src="{{ $imgUrl }}" alt="Đính kèm" style="width:100%;height:auto;display:block;">
                                                        </div>
                                                    @endif
                                                    <div class="chat-meta">
                                                        <span>{{ $message->is_admin ? 'Admin' : 'Bạn' }}</span>
                                                        <span>{{ $message->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="chat-empty">
                                            <i class="fas fa-comments fa-2x mb-3"></i>
                                            <p>Hãy gửi tin nhắn đầu tiên để bắt đầu cuộc trò chuyện với admin.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <form id="admin-chat-form" class="chat-form" method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data" onsubmit="sendAdminMessage(event)">
                                    @csrf
                                    <textarea id="admin-message-input" name="message" placeholder="Nhập tin nhắn của bạn...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="error-message" style="color:#f87171;font-size:12px;">{{ $message }}</span>
                                    @enderror
                                    @error('attachments.*')
                                        <span class="error-message" style="color:#f87171;font-size:12px;">{{ $message }}</span>
                                    @enderror
                                    <input type="file" name="attachments[]" id="user-attachments" accept="image/*" class="hidden" multiple>
                                    <div class="chat-actions">
                                        <button type="button" class="image-btn" onclick="document.getElementById('user-attachments').click();">
                                            <i class="fas fa-image"></i>
                                        </button>
                                        <button type="submit">
                                            <i class="fas fa-paper-plane"></i>
                                            Gửi tin nhắn
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-error" style="margin-top:16px">
                        <i class="fas fa-exclamation-circle"></i>
                        Bạn cần đăng nhập để nhắn tin trực tiếp với admin.
                        <a href="{{ route('login') }}" class="cta-button" style="margin-left:8px">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="cta-button secondary" style="margin-left:8px">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</main>

<!-- Modal xem ảnh lớn (User) -->
<div id="user-image-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="position:relative; max-width:960px; max-height:90vh; margin:0 16px;">
        <button type="button"
                onclick="closeUserImageModal()"
                style="position:absolute; top:-40px; right:0; color:#fff; font-size:13px; padding:6px 12px; border-radius:999px; background:rgba(0,0,0,0.5); border:none; cursor:pointer;">
            Đóng
        </button>
        <img id="user-image-modal-img" src="" alt="Xem ảnh"
             style="max-width:100%; max-height:90vh; border-radius:16px; box-shadow:0 20px 50px rgba(15,23,42,0.6); background:#fff;">
    </div>
</div>

@push('scripts')
<script>
    function openUserImageModal(src) {
        const modal = document.getElementById('user-image-modal');
        const img = document.getElementById('user-image-modal-img');
        if (!modal || !img) return;
        img.src = src;
        modal.style.display = 'flex';
    }

    function closeUserImageModal() {
        const modal = document.getElementById('user-image-modal');
        const img = document.getElementById('user-image-modal-img');
        if (!modal || !img) return;
        img.src = '';
        modal.style.display = 'none';
    }

    document.addEventListener('click', function (event) {
        const modal = document.getElementById('user-image-modal');
        const img = document.getElementById('user-image-modal-img');
        if (!modal || modal.style.display === 'none') return;
        if (!img.contains(event.target)) {
            closeUserImageModal();
        }
    });

    // Khi vào trang, tự cuộn xuống tin nhắn mới nhất trong box chat người dùng
    document.addEventListener('DOMContentLoaded', function () {
        const adminChatWindow = document.querySelector('#admin-chat-panel .chat-window');
        if (adminChatWindow) {
            adminChatWindow.scrollTop = adminChatWindow.scrollHeight;
        }
    });

    // Chuyển chế độ chat AI <-> Admin
    function switchChatMode(mode) {
        const aiPanel = document.getElementById('ai-chat-panel');
        const adminPanel = document.getElementById('admin-chat-panel');
        const toggleAi = document.getElementById('toggle-ai');
        const toggleAdmin = document.getElementById('toggle-admin');
        const sidebarItems = document.querySelectorAll('.chat-conversation-item');

        if (!aiPanel || !adminPanel || !toggleAi || !toggleAdmin) return;

        if (mode === 'admin') {
            aiPanel.style.display = 'none';
            adminPanel.style.display = 'block';
            toggleAi.classList.remove('active');
            toggleAdmin.classList.add('active');

            // Khi chuyển sang tab Admin, tự cuộn xuống tin nhắn mới nhất
            const adminChatWindow = adminPanel.querySelector('.chat-window');
            if (adminChatWindow) {
                adminChatWindow.scrollTop = adminChatWindow.scrollHeight;
            }
        } else {
            aiPanel.style.display = 'block';
            adminPanel.style.display = 'none';
            toggleAi.classList.add('active');
            toggleAdmin.classList.remove('active');
        }

        sidebarItems.forEach(item => {
            const target = item.getAttribute('data-chat-target');
            if (target === mode) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Gửi tin nhắn cho AI (AJAX)
    async function sendAiMessage(event) {
        event.preventDefault();
        const input = document.getElementById('ai-message-input');
        const chatWindow = document.getElementById('ai-chat-window');
        if (!input || !chatWindow) return;

        const text = input.value.trim();
        if (!text) return;

        // Thêm tin nhắn người dùng vào UI
        const userRow = document.createElement('div');
        userRow.className = 'chat-row user';
        userRow.innerHTML = `
            <div class="chat-avatar-wrapper">
                <div class="chat-avatar">🙂</div>
                <div class="chat-bubble">
                    <p>${text.replace(/</g, '&lt;')}</p>
                    <div class="chat-meta">
                        <span>Bạn</span>
                        <span>Vừa xong</span>
                    </div>
                </div>
            </div>
        `;
        chatWindow.appendChild(userRow);
        chatWindow.scrollTop = chatWindow.scrollHeight;
        input.value = '';

        // Gửi request tới server
        try {
            const response = await fetch('{{ route('contact.ai-message') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await response.json();

            const aiRow = document.createElement('div');
            aiRow.className = 'chat-row ai';
            let replyText = data.reply || 'Xin lỗi, hiện tại trợ lý AI đang bận. Vui lòng thử lại sau.';

            aiRow.innerHTML = `
                <div class="chat-avatar-wrapper">
                    <div class="chat-avatar">🤖</div>
                    <div class="chat-bubble">
                        <p>${replyText.replace(/</g, '&lt;')}</p>
                        <div class="chat-meta">
                            <span>AI</span>
                            <span>Vừa xong</span>
                        </div>
                    </div>
                </div>
            `;
            chatWindow.appendChild(aiRow);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        } catch (e) {
            const errorRow = document.createElement('div');
            errorRow.className = 'chat-row ai';
            errorRow.innerHTML = `
                <div class="chat-avatar-wrapper">
                    <div class="chat-avatar">🤖</div>
                    <div class="chat-bubble">
                        <p>Xin lỗi, có lỗi xảy ra khi kết nối tới trợ lý AI. Bạn có thể thử lại sau hoặc chuyển sang chat với Admin.</p>
                        <div class="chat-meta">
                            <span>AI</span>
                            <span>Lỗi kết nối</span>
                        </div>
                    </div>
                </div>
            `;
            chatWindow.appendChild(errorRow);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
    }

    // Điền nhanh câu hỏi gợi ý cho AI
    function fillAiSuggestion(text) {
        const input = document.getElementById('ai-message-input');
        if (!input) return;
        input.value = text;
        input.focus();
    }

    // Gửi tin nhắn cho Admin (AJAX)
    async function sendAdminMessage(event) {
        event.preventDefault();
        const form = document.getElementById('admin-chat-form');
        const textarea = document.getElementById('admin-message-input');
        const chatWindow = document.querySelector('#admin-chat-panel .chat-window');
        if (!form || !chatWindow) return;

        const text = textarea.value.trim();
        const fileInput = document.getElementById('user-attachments');
        const hasFiles = fileInput && fileInput.files && fileInput.files.length > 0;

        if (!text && !hasFiles) {
            alert('Vui lòng nhập nội dung hoặc chọn hình ảnh.');
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!data.success) {
                alert(data.message || 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
                return;
            }

            (data.messages || []).forEach(m => {
                const row = document.createElement('div');
                row.className = 'chat-row user';
                const safeText = (m.text || '').replace(/</g, '&lt;');

                let attachmentHtml = '';
                if (m.attachment_url) {
                    attachmentHtml = `
                        <div style="margin-top:10px;border-radius:12px;max-width:180px;overflow:hidden;cursor:pointer;box-shadow:0 4px 10px rgba(15,23,42,.18);"
                             onclick="event.stopPropagation(); openUserImageModal('${m.attachment_url}')">
                            <img src="${m.attachment_url}" alt="Đính kèm" style="width:100%;height:auto;display:block;">
                        </div>
                    `;
                }

                row.innerHTML = `
                    <div class="chat-avatar-wrapper">
                        <div class="chat-avatar">🙂</div>
                        <div class="chat-bubble">
                            ${safeText ? `<p>${safeText}</p>` : ''}
                            ${attachmentHtml}
                            <div class="chat-meta">
                                <span>Bạn</span>
                                <span>Vừa xong</span>
                            </div>
                        </div>
                    </div>
                `;

                chatWindow.appendChild(row);
            });

            chatWindow.scrollTop = chatWindow.scrollHeight;
            textarea.value = '';
            if (fileInput) fileInput.value = '';
        } catch (e) {
            alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
        }
    }
</script>
@endpush

@endsection
