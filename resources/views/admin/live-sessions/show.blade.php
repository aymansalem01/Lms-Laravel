<x-layouts.dashboard>
    <x-slot name="title">{{ $liveSession->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.live-sessions.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to sessions
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $liveSession->title }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $liveSession->course->title ?? '—' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.live-sessions.edit', $liveSession) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.live-sessions.destroy', $liveSession) }}" onsubmit="return confirm('Delete this session?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-surface-700/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Scheduled At</p>
                        <p class="text-white">{{ $liveSession->scheduled_at ? \Carbon\Carbon::parse($liveSession->scheduled_at)->format('M d, Y H:i') : '—' }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Provider</p>
                        <p class="text-white">{{ $liveSession->provider ?? '—' }}</p>
                    </div>
                    @if($liveSession->room_url)
                        <div class="bg-surface-700/50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Room URL</p>
                            <a href="{{ $liveSession->room_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $liveSession->room_url }}</a>
                        </div>
                    @endif
                    @if($liveSession->recording_url)
                        <div class="bg-surface-700/50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Recording URL</p>
                            <a href="{{ $liveSession->recording_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $liveSession->recording_url }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $liveSession->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $liveSession->course->instructor->name ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Status</h4>
                @php $isPast = $liveSession->scheduled_at && \Carbon\Carbon::parse($liveSession->scheduled_at)->isPast(); @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isPast ? 'bg-gray-500/20 text-gray-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                    {{ $isPast ? 'Past' : 'Upcoming' }}
                </span>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
