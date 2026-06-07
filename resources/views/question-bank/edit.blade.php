<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Question') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.question-bank.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to question bank') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Edit Question') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('courses.question-bank.update', [$course, $questionBankItem]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Question Type') }}</label>
                        <select name="type" id="type" onchange="toggleBankOptions(this.value)"
                                class="input-dashboard">
                            <option value="multiple_choice" {{ old('type', $questionBankItem->type) === 'multiple_choice' ? 'selected' : '' }}>{{ __('Multiple Choice') }}</option>
                            <option value="true_false" {{ old('type', $questionBankItem->type) === 'true_false' ? 'selected' : '' }}>{{ __('True/False') }}</option>
                            <option value="short_answer" {{ old('type', $questionBankItem->type) === 'short_answer' ? 'selected' : '' }}>{{ __('Short Answer') }}</option>
                            <option value="long_answer" {{ old('type', $questionBankItem->type) === 'long_answer' ? 'selected' : '' }}>{{ __('Long Answer') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Default Points') }}</label>
                        <input type="number" name="points" id="points" value="{{ old('points', $questionBankItem->points) }}" min="1"
                               class="input-dashboard">
                    </div>
                </div>

                <div>
                    <label for="question" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Question') }}</label>
                    <textarea name="question" id="question" rows="3" required
                              class="input-dashboard resize-none">{{ old('question', $questionBankItem->question) }}</textarea>
                </div>

                <div id="bank-options-container">
                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Options') }}</label>
                    @foreach(($questionBankItem->options ?? ['', '', '', '']) as $optIndex => $option)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" name="options[]"
                               class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500"
                               placeholder="{{ __('Option') }} {{ chr(65 + $optIndex) }}" value="{{ $option }}">
                        <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                            <input type="radio" name="correct_answer" value="{{ $optIndex }}" class="accent-brand-500"
                                   {{ $questionBankItem->correct_answer !== null && (int)$questionBankItem->correct_answer === $optIndex ? 'checked' : '' }}>
                            {{ __('Correct') }}
                        </label>
                    </div>
                    @endforeach
                </div>

                <div id="bank-true-false-container" class=" space-y-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Correct Answer') }}</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="correct_answer" value="true" class="accent-brand-500"
                                   {{ $questionBankItem->correct_answer === 'true' ? 'checked' : '' }}>
                            {{ __('True') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="correct_answer" value="false" class="accent-brand-500"
                                   {{ $questionBankItem->correct_answer === 'false' ? 'checked' : '' }}>
                            {{ __('False') }}
                        </label>
                    </div>
                </div>

                <div id="bank-short-answer-container" class=" space-y-2">
                    <label for="correct_answer_short" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Correct Answer') }}</label>
                    <input type="text" name="correct_answer" id="correct_answer_short"
                           class="input-dashboard"
                           value="{{ old('correct_answer', $questionBankItem->type === 'short_answer' ? $questionBankItem->correct_answer : '') }}">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Update Question') }}
                </button>
                <a href="{{ route('courses.question-bank.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleBankOptions(type) {
            document.getElementById('bank-options-container').classList.toggle('hidden', type !== 'multiple_choice');
            document.getElementById('bank-true-false-container').classList.toggle('hidden', type !== 'true_false');
            document.getElementById('bank-short-answer-container').classList.toggle('hidden', type !== 'short_answer');
        }
        toggleBankOptions(document.getElementById('type').value);
    </script>
    @endpush
</x-layouts.dashboard>
