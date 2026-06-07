<x-layouts.dashboard>
    <x-slot name="title">Discussions — {{ $course->title }} — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Discussions</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.discussions.create', $course) }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Topic
        </a>
    </div>

    @forelse($topics as $topic)
        <a href="{{ route('courses.discussions.show', [$course, $topic]) }}"
           class="block bg-surface-800 border border-white/10 rounded-xl p-6 mb-3 hover:border-white/20 transition-colors group">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors flex items-center gap-2">
                        @if($topic->is_pinned)
                            <svg class="w-4 h-4 text-brand-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                        @endif
                        @if($topic->is_locked)
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                        @endif
                        {{ $topic->title }}
                    </h3>
                    <div class="flex items-center gap-3 mt-3 text-sm text-gray-400">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-brand-500/30 flex items-center justify-center text-[10px] font-bold text-brand-300">
                                {{ strtoupper(substr($topic->user->name ?? '?', 0, 1)) }}
                            </div>
                            <span>{{ $topic->user->name ?? 'Unknown' }}</span>
                            @if($topic->user && in_array($topic->user->role, ['instructor', 'admin']))
                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ $topic->user->role === 'admin' ? 'bg-purple-500/10 text-purple-400' : 'bg-brand-500/10 text-brand-400' }}">
                                    {{ $topic->user->role === 'admin' ? 'Admin' : 'Instructor' }}
                                </span>
                            @endif
                        </div>
                        <span class="text-gray-600">&middot;</span>
                        <span>{{ $topic->replies_count ?? 0 }} {{ __('replies') }}</span>
                        <span class="text-gray-600">&middot;</span>
                        <span>{{ $topic->last_activity_at?->diffForHumans() ?? $topic->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-400 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
    @empty
        <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-gray-400 font-medium">No discussions yet</p>
            <p class="text-gray-600 text-sm mt-1">Be the first to start a conversation.</p>
        </div>
    @endforelse


</x-layouts.dashboard>
