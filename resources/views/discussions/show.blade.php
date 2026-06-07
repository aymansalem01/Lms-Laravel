<x-layouts.dashboard>
    <x-slot name="title">{{ $discussion->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.discussions.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to discussions
        </a>
    </div>

    @php
        $canManage = auth()->user()->isInstructorOrAdmin();
    @endphp

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-brand-500/30 flex items-center justify-center text-sm font-bold text-brand-300 shrink-0">
                {{ strtoupper(substr($discussion->user->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-medium text-white">{{ $discussion->user->name ?? 'Unknown' }}</span>
                    @if($discussion->user && in_array($discussion->user->role, ['instructor', 'admin']))
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ $discussion->user->role === 'admin' ? 'bg-purple-500/10 text-purple-400' : 'bg-brand-500/10 text-brand-400' }}">
                            {{ $discussion->user->role === 'admin' ? 'Admin' : 'Instructor' }}
                        </span>
                    @endif
                    <span class="text-xs text-gray-500">{{ $discussion->created_at?->diffForHumans() ?? '—' }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2 mb-3">
                    @if($discussion->is_pinned)
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                            Pinned
                        </span>
                    @endif
                    @if($discussion->is_locked)
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-500/10 text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                            Locked
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-white mb-3">{{ $discussion->title }}</h2>
                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $discussion->content }}</div>

                @if($canManage)
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-white/10">
                        <form method="POST" action="{{ route('courses.discussions.pin', [$course, $discussion]) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors {{ $discussion->is_pinned ? 'bg-brand-500/10 text-brand-400 hover:bg-brand-500/20' : 'bg-surface-700 text-gray-400 hover:text-white hover:bg-surface-600' }}">
                                {{ $discussion->is_pinned ? 'Unpin' : 'Pin' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('courses.discussions.lock', [$course, $discussion]) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors {{ $discussion->is_locked ? 'bg-gray-500/10 text-gray-400 hover:bg-gray-500/20' : 'bg-surface-700 text-gray-400 hover:text-white hover:bg-surface-600' }}">
                                {{ $discussion->is_locked ? 'Unlock' : 'Lock' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-white mb-4">Replies ({{ $replies->total() ?? 0 }})</h3>

    @forelse($replies as $reply)
        <div class="bg-surface-800/60 border border-white/10 rounded-xl p-5 mb-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-surface-600 flex items-center justify-center text-xs font-bold text-gray-300 shrink-0">
                    {{ strtoupper(substr($reply->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium text-white">{{ $reply->user->name ?? 'Unknown' }}</span>
                        @if($reply->user && in_array($reply->user->role, ['instructor', 'admin']))
                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ $reply->user->role === 'admin' ? 'bg-purple-500/10 text-purple-400' : 'bg-brand-500/10 text-brand-400' }}">
                                {{ $reply->user->role === 'admin' ? 'Admin' : 'Instructor' }}
                            </span>
                        @endif
                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $reply->content }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500 text-sm">No replies yet.</div>
    @endforelse

    {{ $replies->links() }}

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mt-6">
        <h4 class="text-white font-semibold mb-4">Post a Reply</h4>
        @if($discussion->is_locked)
            <div class="flex items-center gap-2 text-gray-500 text-sm py-4">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                <span>This topic is locked. No new replies can be added.</span>
            </div>
        @else
            <form method="POST" action="{{ route('courses.discussions.reply', [$course, $discussion]) }}">
                @csrf
                <textarea name="content" rows="4" placeholder="Write your reply..." class="input-dashboard resize-none">{{ old('content') }}</textarea>
                @error('content')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Post Reply</button>
                </div>
            </form>
        @endif
    </div>
</x-layouts.dashboard>
