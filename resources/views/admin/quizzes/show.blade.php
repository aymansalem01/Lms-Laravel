<x-layouts.dashboard>
    <x-slot name="title">{{ $quiz->title }} — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.quizzes.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to quizzes
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Quiz Info --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $quiz->title }}</h2>
                        @if($quiz->description)
                            <p class="text-gray-400 text-sm mt-1">{{ $quiz->description }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.quizzes.toggle-publish', $quiz) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10 {{ $quiz->is_published ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' }}">
                                {{ $quiz->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Max Attempts</p>
                        <p class="text-lg font-bold text-white">{{ $quiz->max_attempts ?? '∞' }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Time Limit</p>
                        <p class="text-lg font-bold text-white">{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'None' }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Questions</p>
                        <p class="text-lg font-bold text-white">{{ $quiz->questions->count() }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Avg Score</p>
                        <p class="text-lg font-bold text-white">{{ $avgScore ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Questions --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Questions</h3>
                @forelse($quiz->questions as $question)
                    <div class="py-3 border-b border-white/5 last:border-0">
                        <div class="flex items-start justify-between">
                            <p class="text-sm text-white">{{ $question->question_text ?? $question->title ?? $question->content }}</p>
                            <span class="text-xs text-gray-500 ml-2 shrink-0">{{ $question->type ?? '—' }}</span>
                        </div>
                        @if($question->options ?? false)
                            <div class="mt-2 space-y-1">
                                @foreach($question->options as $option)
                                    <p class="text-xs {{ isset($option['correct']) && $option['correct'] ? 'text-emerald-400' : 'text-gray-500' }}">
                                        {{ $option['text'] ?? $option['value'] ?? $option }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No questions yet.</p>
                @endforelse
            </div>

            {{-- Attempts --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Attempts ({{ $quiz->attempts->count() }})</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.quizzes.attempts-export', $quiz) }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Attempts CSV
                        </a>
                        <a href="{{ route('admin.quizzes.attempts-export-example') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Example CSV
                        </a>
                    </div>
                </div>
                @forelse($quiz->attempts->sortByDesc('created_at') as $attempt)
                    <div class="flex items-center justify-between py-2.5 border-b border-white/5 last:border-0">
                        <div>
                            <p class="text-sm text-white">{{ $attempt->student->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $attempt->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold {{ $attempt->score >= 70 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $attempt->score ?? '—' }}%
                            </p>
                            @if($attempt->is_completed ?? true)
                                <span class="text-xs text-emerald-500">Completed</span>
                            @else
                                <span class="text-xs text-amber-500">In Progress</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No attempts yet.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $quiz->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $quiz->course->instructor->name ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Status</h4>
                @if($quiz->is_published)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Published</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">Draft</span>
                @endif
            </div>
        </div>
    </div>
</x-layouts.dashboard>
