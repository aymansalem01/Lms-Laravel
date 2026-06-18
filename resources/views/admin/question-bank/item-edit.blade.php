<x-layouts.dashboard>
    <x-slot name="title">Edit Question — {{ $questionBank->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.question-bank.show', $questionBank) }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $questionBank->name }}
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Question</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $questionBank->name }}</p>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.question-bank.items.update', [$questionBank, $questionBankItem]) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-300 mb-1.5">Question Type</label>
                        <select name="type" id="type" onchange="toggleBankOptions(this.value)"
                                class="input-dashboard">
                            <option value="multiple_choice" {{ old('type', $questionBankItem->type) === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('type', $questionBankItem->type) === 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="short_answer" {{ old('type', $questionBankItem->type) === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="long_answer" {{ old('type', $questionBankItem->type) === 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                        </select>
                    </div>
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-300 mb-1.5">Points</label>
                        <input type="number" name="points" id="points" value="{{ old('points', $questionBankItem->points) }}" min="1"
                               class="input-dashboard">
                    </div>
                </div>

                <div>
                    <label for="question" class="block text-sm font-medium text-gray-300 mb-1.5">Question</label>
                    <textarea name="question" id="question" rows="3" required
                              class="input-dashboard resize-none"
                              placeholder="Enter your question...">{{ old('question', $questionBankItem->question) }}</textarea>
                </div>

                <div id="bank-options-container" class="{{ $questionBankItem->type !== 'multiple_choice' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Options</label>
                    @php $itemOptions = $questionBankItem->options ?? []; @endphp
                    @for($opt = 0; $opt < 4; $opt++)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" name="options[]"
                               class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500"
                               placeholder="Option {{ chr(65 + $opt) }}" value="{{ old("options.$opt", $itemOptions[$opt] ?? '') }}">
                        <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                            <input type="radio" name="correct_answer" value="{{ $opt }}" class="accent-brand-500" {{ old('correct_answer', $questionBankItem->correct_answer) == $opt ? 'checked' : '' }}>
                            Correct
                        </label>
                    </div>
                    @endfor
                </div>

                <div id="bank-true-false-container" class="{{ $questionBankItem->type !== 'true_false' ? 'hidden' : '' }} space-y-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="correct_answer" value="true" class="accent-brand-500" {{ old('correct_answer', $questionBankItem->correct_answer) === 'true' ? 'checked' : '' }}>
                            True
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="correct_answer" value="false" class="accent-brand-500" {{ old('correct_answer', $questionBankItem->correct_answer) === 'false' ? 'checked' : '' }}>
                            False
                        </label>
                    </div>
                </div>

                <div id="bank-short-answer-container" class="{{ !in_array($questionBankItem->type, ['short_answer', 'long_answer']) ? 'hidden' : '' }} space-y-2">
                    <label for="correct_answer_short" class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                    <input type="text" name="correct_answer" id="correct_answer_short"
                           class="input-dashboard" value="{{ old('correct_answer', $questionBankItem->correct_answer) }}"
                           placeholder="Expected answer...">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    Update Question
                </button>
                <a href="{{ route('admin.question-bank.show', $questionBank) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function toggleBankOptions(type) {
            document.getElementById('bank-options-container').classList.toggle('hidden', type !== 'multiple_choice');
            document.getElementById('bank-true-false-container').classList.toggle('hidden', type !== 'true_false');
            document.getElementById('bank-short-answer-container').classList.toggle('hidden', type !== 'short_answer' && type !== 'long_answer');
        }
    </script>
    @endpush
</x-layouts.dashboard>
