<x-layouts.dashboard>
    <x-slot name="title">{{ $quiz->title }} — {{ $course->name }}</x-slot>

    @php
        $isInstructor = auth()->user()->role === 'instructor';
        $attempts = $quiz->attempts->where('student_id', auth()->id());
        $bestAttempt = $attempts->sortByDesc('score')->first();
        $canAttempt = !$isInstructor && ($quiz->max_attempts === 0 || $attempts->count() < $quiz->max_attempts);
    @endphp

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quizzes') }}
        </a>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
            </div>
            @if($isInstructor)
                <div class="flex items-center gap-2">
                    <a href="{{ route('courses.quizzes.edit', [$course, $quiz]) }}"
                       class="bg-surface-700 hover:bg-surface-600 text-white font-medium rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ __('Edit') }}
                    </a>
                    <form method="POST" action="{{ route('courses.quizzes.destroy', [$course, $quiz]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this quiz?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-500/10 hover:bg-red-500/20 text-red-400 font-medium rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Description') }}</h2>
                <p class="text-gray-300 text-sm leading-relaxed">{{ $quiz->description }}</p>
            </div>

            @if($isInstructor)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-white">{{ __('Questions') }} ({{ count($quiz->questions) }})</h2>
                        <a href="{{ route('courses.quizzes.results', [$course, $quiz]) }}"
                           class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            {{ __('View Results') }}
                        </a>
                    </div>
                    @if(count($quiz->questions) === 0)
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">{{ __('No questions added yet.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($quiz->questions as $index => $question)
                                <div class="bg-surface-700 rounded-xl px-4 py-3 flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-500 font-mono">Q{{ $index + 1 }}</span>
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-brand-500/10 text-brand-400">{{ __(ucwords(str_replace('_', ' ', $question->type))) }}</span>
                                        </div>
                                        <p class="text-sm text-gray-300 mt-1 truncate">{{ $question->question }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 shrink-0 ml-3">{{ $question->points }} {{ __('pts') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                {{-- Student View --}}
                @if($bestAttempt)
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">{{ __('Your Results') }}</h2>
                            <a href="{{ route('courses.quizzes.results', [$course, $quiz]) }}"
                               class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1.5">
                                {{ __('View Full Results') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl gb flex items-center justify-center">
                                <span class="text-xl font-bold text-white">{{ number_format($bestAttempt->score, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">{{ __('Best score out of') }} {{ $quiz->questions->sum('points') }}</p>
                                <p class="text-xs text-gray-500">{{ $attempts->count() }}/{{ $quiz->max_attempts ?: '∞' }} {{ __('attempts used') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($canAttempt)
                    <a href="{{ route('courses.quizzes.take', [$course, $quiz]) }}"
                       class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-8 py-3 text-base transition-colors duration-200 flex items-center gap-2 inline-flex">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Start Quiz') }}
                    </a>
                @elseif(!$bestAttempt)
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl px-4 py-3">
                        <p class="text-sm text-yellow-400 font-medium">{{ __('You have used all your attempts for this quiz.') }}</p>
                    </div>
                @endif
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white mb-3">{{ __('Quiz Info') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Questions') }}</span>
                        <span class="text-gray-300">{{ count($quiz->questions) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Total Points') }}</span>
                        <span class="text-gray-300">{{ $quiz->questions->sum('points') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Max Attempts') }}</span>
                        <span class="text-gray-300">{{ $quiz->max_attempts ?: __('Unlimited') }}</span>
                    </div>
                    @if($quiz->time_limit)
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Time Limit') }}</span>
                            <span class="text-gray-300">{{ $quiz->time_limit }} {{ __('min') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Status') }}</span>
                        <span class="{{ $quiz->is_published ? 'text-green-400' : 'text-yellow-400' }}">{{ $quiz->is_published ? __('Published') : __('Draft') }}</span>
                    </div>
                </div>
            </div>

            @if(!$isInstructor && $attempts->count() > 0)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-white mb-3">{{ __('Attempt History') }}</h3>
                    <div class="space-y-2">
                        @foreach($attempts->sortByDesc('created_at') as $attempt)
                            <div class="flex items-center justify-between py-2 {{ !$loop->first ? 'border-t border-white/10' : '' }}">
                                <span class="text-xs text-gray-500">{{ $attempt->created_at->format('M d, Y') }}</span>
                                <span class="text-sm font-medium {{ $attempt->score >= $quiz->questions->sum('points') * 0.6 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($attempt->score, 1) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
