<x-layouts.dashboard>
    <x-slot name="title">{{ $session->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('live.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to live sessions
        </a>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $session->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ $session->course->title ?? 'General' }}</p>
                <div class="flex items-center gap-3 mt-3 text-sm text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $session->scheduled_at ? $session->scheduled_at->format('M d, Y g:i A') : 'Not scheduled' }}
                    </span>
                    <span class="text-gray-600">&middot;</span>
                    <span class="capitalize">{{ $session->provider }}</span>
                </div>
                @if($session->scheduled_at && $session->scheduled_at->isFuture())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 mt-3">Upcoming</span>
                @elseif($session->scheduled_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 mt-3">Past</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 mt-3">Unscheduled</span>
                @endif
            </div>
        </div>
    </div>

    @if($session->provider === 'livekit' && $session->scheduled_at && $session->scheduled_at->isFuture())
        <x-livekit-room :token="''" :host="config('services.livekit.host')" :roomName="$session->room_url ?? $session->title" :sessionId="$session->id" />
    @elseif($session->room_url && $session->scheduled_at && $session->scheduled_at->isFuture())
        <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden mb-6">
            <div class="p-4 border-b border-white/10 flex items-center justify-between">
                <span class="text-sm font-medium text-white">Live Room</span>
                <a href="{{ $session->room_url }}" target="_blank" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-5 py-2 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Open in new tab
                </a>
            </div>
            <iframe src="{{ $session->room_url }}" class="w-full h-[500px] border-0" allow="camera;microphone;fullscreen" loading="lazy"></iframe>
        </div>
    @elseif($session->recording_url)
        <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden mb-6">
            <div class="p-4 border-b border-white/10">
                <span class="text-sm font-medium text-white">Recording</span>
            </div>
            <div class="p-6">
                <a href="{{ $session->recording_url }}" target="_blank" class="inline-flex items-center gap-2 bg-surface-600 hover:bg-surface-700 text-white rounded-xl px-6 py-3 text-sm font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Watch Recording
                </a>
            </div>
        </div>
    @else
        <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <p class="text-gray-400 font-medium">No room or recording available</p>
            <p class="text-gray-600 text-sm mt-1">Check back closer to the scheduled time.</p>
        </div>
    @endif
</x-layouts.dashboard>
