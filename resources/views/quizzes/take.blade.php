<x-layouts.dashboard>
    <x-slot name="title">{{ $quiz->title }} — {{ __('Take Quiz') }}</x-slot>

    @php
        $questions = $quiz->randomize_questions ? $quiz->questions->shuffle() : $quiz->questions;
        $totalPoints = $questions->sum('points');
    @endphp

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quiz') }}
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ count($quiz->questions) }} {{ __('questions') }} · {{ $totalPoints }} {{ __('total points') }}</p>
            </div>
            @if($quiz->time_limit)
                <div x-data="timer({{ $quiz->time_limit * 60 }})" x-init="start()" class="flex items-center gap-2 bg-surface-800 border border-white/10 rounded-xl px-4 py-2">
                    <svg class="w-4 h-4 text-coral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-mono font-semibold" x-text="formatTime()" :class="timeLeft < 60 ? 'text-red-400' : 'text-gray-300'"></span>
                </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('courses.quizzes.attempt', [$course, $quiz]) }}" onsubmit="return confirm('{{ __('Are you sure you want to submit? You cannot change your answers after submission.') }}')">
        @csrf

        <div class="space-y-6">
            @foreach($questions as $index => $question)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-6" id="question-{{ $index }}">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center text-sm font-bold shrink-0">{{ $index + 1 }}</span>
                            <div>
                                <h3 class="text-white font-medium">{{ $question->question }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $question->points }} {{ __('points') }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-brand-500/10 text-brand-400 shrink-0">{{ __(ucwords(str_replace('_', ' ', $question->type))) }}</span>
                    </div>

                    @if($question->type === 'multiple_choice')
                        <div class="space-y-2 ml-11">
                            @foreach($question->options as $optIndex => $option)
                                <label class="flex items-center gap-3 bg-surface-700 border border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:border-brand-500/50 transition-all duration-200 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/10">
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $optIndex }}"
                                           class="accent-brand-500 shrink-0">
                                    <span class="text-sm text-gray-300">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->type === 'true_false')
                        <div class="space-y-2 ml-11">
                            <label class="flex items-center gap-3 bg-surface-700 border border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:border-brand-500/50 transition-all duration-200 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/10">
                                <input type="radio" name="answers[{{ $index }}]" value="true" class="accent-brand-500 shrink-0">
                                <span class="text-sm text-gray-300">{{ __('True') }}</span>
                            </label>
                            <label class="flex items-center gap-3 bg-surface-700 border border-white/10 rounded-xl px-4 py-3 cursor-pointer hover:border-brand-500/50 transition-all duration-200 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/10">
                                <input type="radio" name="answers[{{ $index }}]" value="false" class="accent-brand-500 shrink-0">
                                <span class="text-sm text-gray-300">{{ __('False') }}</span>
                            </label>
                        </div>
                    @elseif($question->type === 'short_answer')
                        <div class="ml-11">
                            <input type="text" name="answers[{{ $index }}]"
                                   class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500"
                                   placeholder="{{ __('Type your answer...') }}">
                        </div>
                    @elseif($question->type === 'long_answer')
                        <div class="ml-11">
                            <textarea name="answers[{{ $index }}]" rows="4"
                                      class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 resize-none"
                                      placeholder="{{ __('Write your answer...') }}"></textarea>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 mt-6">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <p class="text-sm text-gray-400">{{ __('Once submitted, you will not be able to change your answers. Make sure you have answered all questions before submitting.') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-8 py-3 text-base transition-colors duration-200">
                    {{ __('Submit Quiz') }}
                </button>
                <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>

    @if($quiz->time_limit)
    @push('scripts')
    <script>
        function timer(seconds) {
            return {
                timeLeft: seconds,
                start() {
                    setInterval(() => {
                        if (this.timeLeft > 0) this.timeLeft--;
                        else document.querySelector('form').submit();
                    }, 1000);
                },
                formatTime() {
                    const m = Math.floor(this.timeLeft / 60);
                    const s = this.timeLeft % 60;
                    return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                }
            };
        }
    </script>
    @endpush
    @endif
</x-layouts.dashboard>
