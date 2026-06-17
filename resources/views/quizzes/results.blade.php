<x-layouts.dashboard>
    <x-slot name="title">{{ __('Quiz Results') }} — {{ $quiz->title }}</x-slot>

    @php
        $totalPoints = $quiz->questions->sum('points');
        $percentage = $totalPoints > 0 ? ($attempt->score / $totalPoints) * 100 : 0;
        $passed = $percentage >= 60;
    @endphp

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quiz') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Quiz Results') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $quiz->title }}</p>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-2xl p-8 mb-6">
        @if($quiz->show_results || auth()->user()->isInstructorOrAdmin())
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 rounded-2xl gb flex items-center justify-center mb-4">
                    <span class="text-3xl font-bold text-white">{{ number_format($attempt->score, 1) }}</span>
                </div>
                <p class="text-lg text-gray-400 mb-1">{{ __('out of') }} {{ $totalPoints }} {{ __('points') }}</p>

                <div class="w-full max-w-md mt-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm text-gray-500">{{ __('Score') }}</span>
                        <span class="text-sm font-semibold text-white">{{ number_format($percentage, 0) }}%</span>
                    </div>
                    <div class="w-full bg-surface-700 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $passed ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="mt-4">
                    @if($passed)
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-400 bg-green-500/10 px-4 py-1.5 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Passed') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-400 bg-red-500/10 px-4 py-1.5 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Failed') }}
                        </span>
                    @endif
                </div>

                @if($attempt->created_at)
                    <p class="text-xs text-gray-500 mt-4">{{ __('Submitted') }} {{ $attempt->created_at->diffForHumans() }}</p>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center text-center py-6">
                <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-lg text-gray-300">{{ __('Quiz Submitted') }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ __('Your results are not yet available. Please check back later.') }}</p>
                @if($attempt->created_at)
                    <p class="text-xs text-gray-500 mt-4">{{ __('Submitted') }} {{ $attempt->created_at->diffForHumans() }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Questions Breakdown --}}
    @if($quiz->show_results || auth()->user()->isInstructorOrAdmin())
    <div class="space-y-4">
        @foreach($quiz->questions as $index => $question)
            @php
                $answer = $attempt->answers[$index] ?? null;
                $isCorrect = false;
                $showResult = in_array($question->type, ['multiple_choice', 'true_false']);

                if ($showResult && $answer) {
                    if ($question->type === 'multiple_choice') {
                        $isCorrect = isset($answer['selected']) && $question->correct_answer !== null && (int)$answer['selected'] === (int)$question->correct_answer;
                    } elseif ($question->type === 'true_false') {
                        $isCorrect = isset($answer['value']) && $answer['value'] === $question->correct_answer;
                    }
                }

                $feedback = $answer['feedback'] ?? null;
                $awardedPoints = $answer['points'] ?? 0;
            @endphp
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full {{ $showResult ? ($isCorrect ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400') : 'bg-brand-500/20 text-brand-400' }} flex items-center justify-center text-sm font-bold shrink-0">
                            {{ $showResult ? ($isCorrect ? '✓' : '✗') : ($index + 1) }}
                        </span>
                        <div>
                            <h3 class="text-white font-medium">{{ $question->question }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $question->points }} {{ __('points') }} · {{ __(ucwords(str_replace('_', ' ', $question->type))) }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-medium {{ $showResult ? ($isCorrect ? 'text-green-400' : 'text-red-400') : 'text-yellow-400' }}">
                        {{ number_format($awardedPoints, 1) }}/{{ $question->points }}
                    </span>
                </div>

                @if($showResult && $question->type === 'multiple_choice' && isset($question->options))
                    <div class="ml-11 space-y-2">
                        @foreach($question->options as $optIndex => $option)
                            @php
                                $isSelected = isset($answer['selected']) && $answer['selected'] == $optIndex;
                                $isCorrectOpt = $question->correct_answer !== null && (int)$question->correct_answer === $optIndex;
                            @endphp
                            <div class="flex items-center gap-3 bg-surface-700 border rounded-xl px-4 py-3
                                {{ $isCorrectOpt ? 'border-green-500/50 bg-green-500/10' : ($isSelected && !$isCorrectOpt ? 'border-red-500/50 bg-red-500/10' : 'border-white/10') }}">
                                <span class="text-sm text-gray-300">{{ $option }}</span>
                                @if($isCorrectOpt)
                                    <svg class="w-4 h-4 text-green-400 shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($isSelected)
                                    <svg class="w-4 h-4 text-red-400 shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($showResult && $question->type === 'true_false')
                    <div class="ml-11 flex items-center gap-4">
                        @foreach(['true' => __('True'), 'false' => __('False')] as $val => $label)
                            @php
                                $isSelected = isset($answer['value']) && $answer['value'] === $val;
                                $isCorrectVal = $question->correct_answer === $val;
                            @endphp
                            <div class="flex items-center gap-2 bg-surface-700 border rounded-xl px-4 py-3
                                {{ $isCorrectVal ? 'border-green-500/50 bg-green-500/10' : ($isSelected && !$isCorrectVal ? 'border-red-500/50 bg-red-500/10' : 'border-white/10') }}">
                                <span class="text-sm text-gray-300">{{ $label }}</span>
                                @if($isCorrectVal)
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($isSelected)
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(in_array($question->type, ['short_answer', 'long_answer']))
                    <div class="ml-11">
                        <div class="bg-surface-700 rounded-xl px-4 py-3 mb-2">
                            <p class="text-xs text-gray-500 mb-1">{{ __('Your answer') }}</p>
                            <p class="text-sm text-gray-300">{{ $answer['text'] ?? '—' }}</p>
                        </div>
                        @if($feedback)
                            <div class="bg-brand-500/10 border border-brand-500/20 rounded-xl px-4 py-3">
                                <p class="text-xs text-brand-400 mb-1">{{ __('Instructor Feedback') }}</p>
                                <p class="text-sm text-gray-300">{{ $feedback }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    @auth
            @if(auth()->user()->role !== 'instructor' && !is_null($quiz->max_attempts) && $quiz->attempts->where('student_id', auth()->id())->count() < $quiz->max_attempts)
            <div class="mt-6 text-center">
                <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}"
                   class="inline-flex items-center gap-2 bg-surface-700 hover:bg-surface-600 text-white font-medium rounded-xl px-6 py-2.5 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    {{ __('Retake Quiz') }}
                </a>
            </div>
        @endif
    @endauth
</x-layouts.dashboard>
