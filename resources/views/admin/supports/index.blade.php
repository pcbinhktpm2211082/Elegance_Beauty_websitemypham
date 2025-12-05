@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .chat-shell {
        min-height: calc(100vh - 140px);
        background:#f3f4f6;      /* nền xám nhạt hòa với dashboard */
        border-radius:10px;      /* bo góc nhẹ cho khung chat */
        overflow:hidden;
        box-shadow:none;         /* giữ giao diện phẳng, hiện đại */
    }
    .chat-sidebar {width:340px; max-width:100%; background:#ffffff; color:#0f172a; border-right:1px solid #e2e8f0;}
    .chat-sidebar a {display:block; list-style:none;}
    .chat-sidebar a.active {background:#eef2ff; border-left:4px solid #6366f1;}
    .chat-sidebar a:hover {background:#f5f7ff;}
    .chat-panel {background:#fff; color:#0f172a;}
    .chat-bubble {max-width:70%; border-radius:18px; padding:12px 16px; box-shadow:0 8px 18px rgba(15,23,42,.08);}
    .chat-bubble.user {margin-left:auto; background:linear-gradient(135deg,#818cf8,#a855f7); color:#fff;}
    .chat-bubble.admin {margin-right:auto; background:#f1f5f9; color:#0f172a;}
    .chat-input {background:#f8fafc; border:1px solid #dbe2f0; border-radius:12px; padding:12px 16px; color:#0f172a; width:100%; min-height:80px;}
    .chat-actions button.ghost {
        background:#e0e7ff;
        color:#4338ca;
        border:none;
        width:44px;
        height:44px;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .chat-actions button.ghost:hover {background:#c7d2fe;}
    .chat-image-wrapper {
        margin-top: 10px;
        border-radius: 12px;
        overflow: hidden;
        width: 140px;
        max-width: 100%;
        box-shadow: 0 4px 10px rgba(15,23,42,.18);
        cursor: pointer;
    }
    .chat-image-wrapper img {
        display: block;
        width: 100%;
        height: auto;
    }
    .chat-messages {
        overflow-y: auto;
        max-height: 60vh;
        padding: 24px;
        gap: 16px;
        display: flex;
        flex-direction: column;
    }
</style>
@endpush

@php use Illuminate\Support\Facades\Storage; @endphp

<div class="px-4 py-6 lg:px-8">
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="chat-shell flex flex-row w-full">
        <!-- Sidebar -->
        <div class="chat-sidebar flex flex-col">
            <div class="p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold">Đoạn chat</h2>
                <p class="text-xs text-gray-400 mt-1">Tin nhắn tới từ khách hàng</p>
            </div>

            <form method="GET" action="{{ route('admin.supports.index') }}" class="p-4 space-y-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-700 placeholder-gray-500" />
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 rounded-xl py-2 text-sm font-semibold shadow-sm"
                            style="background: linear-gradient(135deg,#4f46e5,#6366f1); color:#ffffff;">
                Tìm kiếm
            </button>
                    <a href="{{ route('admin.supports.index') }}"
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm text-gray-500 hover:bg-gray-50">
                        Đặt lại
                </a>
                </div>
        </form>

            <div class="flex-1 overflow-y-auto divide-y divide-[#1f2937]">
                @forelse($supports as $support)
                    @php
                        $linkParams = array_filter([
                            'support_id' => $support->id,
                            'status' => $status,
                            'search' => $search,
                        ], fn($value) => $value !== null && $value !== '');
                    @endphp
                    <a href="{{ route('admin.supports.index', $linkParams) }}"
                       class="block px-4 py-3 {{ optional($currentSupport)->id === $support->id ? 'active' : '' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm">{{ $support->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $support->email }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $support->updated_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 line-clamp-1">{{ \Illuminate\Support\Str::limit($support->message, 80) }}</p>
                    </a>
                @empty
                    <div class="p-6 text-center text-gray-500">Không có cuộc trò chuyện nào.</div>
                @endforelse
            </div>
        </div>

        <!-- Chat window -->
        <div class="chat-panel flex-1 flex flex-col">
            @if($currentSupport)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <div>
                            <p class="font-semibold">{{ $currentSupport->name }}</p>
                            <p class="text-xs text-gray-400">{{ $currentSupport->email }}</p>
                        </div>
                </div>

                <div class="chat-messages" id="admin-chat-messages"
                     data-fragment-url="{{ route('admin.supports.messages.fragment', $currentSupport) }}">
                    @include('admin.supports._messages', ['support' => $currentSupport, 'messages' => $currentSupport->messages])
                </div>

                <form method="POST" action="{{ route('admin.supports.messages.store', $currentSupport) }}" class="border-t border-gray-200 px-6 py-4 space-y-3" enctype="multipart/form-data">
                    @csrf
                    <textarea name="message" rows="3" class="chat-input" placeholder="Aa">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <input type="file" name="attachments[]" id="admin-attachments" accept="image/*" class="hidden" multiple>

                    <div class="flex justify-between items-center chat-actions gap-3">
                        <button type="button" class="ghost" onclick="document.getElementById('admin-attachments').click();">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828a4 4 0 10-5.657-5.657L5.05 11.465" />
                            </svg>
                        </button>

                        <button type="submit"
                                class="px-6 py-2 rounded-full text-sm font-semibold flex items-center gap-2 shadow-sm"
                                style="background: linear-gradient(135deg,#2563eb,#1d4ed8); color:#ffffff;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 5.25l13.5 6.75-13.5 6.75 3-6.75-3-6.75z" />
                            </svg>
                            Gửi tin nhắn
                        </button>
                    </div>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    Chọn một cuộc hội thoại bên trái để bắt đầu nhắn tin.
            </div>
        @endif
        </div>
    </div>
</div>
<!-- Modal xem ảnh lớn -->
<div id="support-image-modal" class="fixed inset-0 bg-black/60 z-40 hidden items-center justify-center">
    <div class="relative max-w-4xl max-h-[90vh] mx-4">
        <button type="button"
                onclick="closeSupportImageModal()"
                class="absolute -top-10 right-0 text-white text-sm px-3 py-1 rounded-full bg-black/50 hover:bg-black/70">
            Đóng
        </button>
        <img id="support-image-modal-img" src="" alt="Xem ảnh" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl bg-white">
    </div>
</div>

<script>
    function openSupportImageModal(src) {
        const modal = document.getElementById('support-image-modal');
        const img = document.getElementById('support-image-modal-img');
        if (!modal || !img) return;
        img.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSupportImageModal() {
        const modal = document.getElementById('support-image-modal');
        const img = document.getElementById('support-image-modal-img');
        if (!modal || !img) return;
        img.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Đóng modal khi click ra ngoài ảnh
    document.addEventListener('click', function (event) {
        const modal = document.getElementById('support-image-modal');
        const img = document.getElementById('support-image-modal-img');
        if (!modal || modal.classList.contains('hidden')) return;

        if (!img.contains(event.target)) {
            closeSupportImageModal();
        }
    });
    // Tự cuộn xuống tin nhắn mới nhất khi load
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('admin-chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    // Polling để cập nhật tin nhắn mới mà không cần reload
    (function () {
        const container = document.getElementById('admin-chat-messages');
        if (!container) return;

        const fragmentUrl = container.dataset.fragmentUrl;
        if (!fragmentUrl) return;

        const fetchMessages = () => {
            const atBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 40;
            fetch(fragmentUrl + "?t=" + Date.now())
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    if (atBottom) {
                        container.scrollTop = container.scrollHeight;
                    }
                })
                .catch(() => {});
        };

        setInterval(fetchMessages, 4000);
    })();
</script>
@endsection
