@php
    $messages = isset($messages)
        ? $messages
        : (isset($currentSupport) ? $currentSupport->messages : collect());
@endphp

@if($messages->isEmpty())
    <div class="text-center text-gray-500 py-20">Chưa có tin nhắn nào.</div>
@else
    @foreach($messages as $message)
        <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
            <div class="chat-bubble {{ $message->is_admin ? 'user' : 'admin' }}">
                @if($message->message)
                    <p class="text-sm">{{ $message->message }}</p>
                @endif

                @if($message->attachment_path)
                    @php $imgUrl = Storage::url($message->attachment_path); @endphp
                    <div class="chat-image-wrapper" onclick="event.stopPropagation(); openSupportImageModal('{{ $imgUrl }}')">
                        <img src="{{ $imgUrl }}" alt="Đính kèm">
                    </div>
                @endif

                <span class="text-[11px] text-gray-400 block mt-2">{{ $message->created_at->format('d/m H:i') }}</span>
            </div>
        </div>
    @endforeach
@endif
