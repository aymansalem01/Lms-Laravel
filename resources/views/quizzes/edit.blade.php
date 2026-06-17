<x-layouts.dashboard>
    <x-slot name="title">{{ __('Edit Quiz') }} — {{ $course->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quizzes') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Edit Quiz') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ $quiz->title }}</p>
    </div>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('courses.quizzes.update', [$course, $quiz]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-6">
                <h2 class="text-lg font-semibold text-white">{{ __('Quiz Details') }}</h2>

                <div>
                    <label for="module_id" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Module') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                    <select name="module_id" id="module_id" class="input-dashboard">
                        <option value="">{{ __('No module') }}</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}" {{ old('module_id', $quiz->module_id) == $module->id ? 'selected' : '' }}>{{ $module->title }}</option>
                        @endforeach
                    </select>
                    @error('module_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" required
                           class="input-dashboard {{ $errors->has('title') ? 'border-red-500' : '' }}">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="3"
                              class="input-dashboard resize-none {{ $errors->has('description') ? 'border-red-500' : '' }}">{{ old('description', $quiz->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Max Attempts') }}</label>
                        <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="0"
                               class="input-dashboard {{ $errors->has('max_attempts') ? 'border-red-500' : '' }}">
                        <p class="text-xs text-gray-500 mt-1">{{ __('0 for unlimited') }}</p>
                        @error('max_attempts') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="time_limit" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Time Limit (minutes)') }}</label>
                        <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="0"
                               class="input-dashboard {{ $errors->has('time_limit') ? 'border-red-500' : '' }}">
                        @error('time_limit') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="is_published" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Status') }}</label>
                        <select name="is_published" id="is_published"
                                class="input-dashboard">
                            <option value="0" {{ old('is_published', $quiz->is_published) == 0 ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            <option value="1" {{ old('is_published', $quiz->is_published) == 1 ? 'selected' : '' }}>{{ __('Published') }}</option>
                        </select>
                        @error('is_published') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="show_results" value="0">
                    <input type="checkbox" name="show_results" id="show_results" value="1" {{ old('show_results', $quiz->show_results) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-white/20 bg-surface-700 text-brand-500 focus:ring-brand-500">
                    <label for="show_results" class="text-sm text-gray-300">{{ __('Show students their results after submission') }}</label>
                </div>
            </div>

            {{-- Questions Section --}}
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">{{ __('Questions') }}</h2>
                </div>

                <div class="space-y-4" id="questions-container">
                    @foreach($quiz->questions as $qIndex => $question)
                        <div class="question-item bg-surface-700 rounded-xl p-4 border border-white/5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-300 question-number">Q{{ $loop->iteration }}</span>
                                <button type="button" onclick="this.closest('.question-item').remove(); updateQuestionNumbers();"
                                        class="text-red-400 hover:text-red-300 text-sm transition-colors">{{ __('Remove') }}</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Question Type') }}</label>
                                    <select name="questions[{{ $qIndex }}][type]" onchange="toggleOptions(this)"
                                            class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                                        <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>{{ __('Multiple Choice') }}</option>
                                        <option value="true_false" {{ $question->type === 'true_false' ? 'selected' : '' }}>{{ __('True/False') }}</option>
                                        <option value="short_answer" {{ $question->type === 'short_answer' ? 'selected' : '' }}>{{ __('Short Answer') }}</option>
                                        <option value="long_answer" {{ $question->type === 'long_answer' ? 'selected' : '' }}>{{ __('Long Answer') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Points') }}</label>
                                    <input type="number" name="questions[{{ $qIndex }}][points]" value="{{ old("questions.$qIndex.points", $question->points) }}" min="1"
                                           class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Question') }}</label>
                                <textarea name="questions[{{ $qIndex }}][question]" rows="2" required
                                          class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500 resize-none">{{ old("questions.$qIndex.question", $question->question) }}</textarea>
                            </div>
                            <div class="options-container space-y-2 {{ $question->type !== 'multiple_choice' ? 'hidden' : '' }}">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Options') }}</label>
                                @foreach($question->options ?? ['', '', '', ''] as $optIndex => $option)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="questions[{{ $qIndex }}][options][]" value="{{ $option }}"
                                               class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                               placeholder="{{ __('Option') }} {{ chr(65 + $optIndex) }}">
                                        <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                                            <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="{{ $optIndex }}" class="accent-brand-500"
                                                   {{ $question->correct_answer !== null && (int)$question->correct_answer === $optIndex ? 'checked' : '' }}>
                                            {{ __('Correct') }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="true-false-container space-y-2 mt-3 {{ $question->type !== 'true_false' ? 'hidden' : '' }}">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Correct Answer') }}</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-300">
                                        <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="true" class="accent-brand-500"
                                               {{ $question->correct_answer === 'true' ? 'checked' : '' }}>
                                        {{ __('True') }}
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-300">
                                        <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="false" class="accent-brand-500"
                                               {{ $question->correct_answer === 'false' ? 'checked' : '' }}>
                                        {{ __('False') }}
                                    </label>
                                </div>
                            </div>
                            <div class="short-answer-container mt-3 {{ $question->type !== 'short_answer' ? 'hidden' : '' }}">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Correct Answer') }}</label>
                                <input type="text" name="questions[{{ $qIndex }}][correct_answer]" value="{{ $question->type === 'short_answer' ? $question->correct_answer : '' }}"
                                       class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" onclick="addQuestion()"
                        class="mt-4 w-full py-3 border-2 border-dashed border-white/10 rounded-xl text-sm text-gray-500 hover:text-brand-400 hover:border-brand-500/50 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Add Question') }}
                </button>
            </div>

            {{-- Import from Question Banks --}}
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Import from Question Banks') }}</h2>

                @php
                    $bankIds = $course->questionBanks()->pluck('question_banks.id');
                    $ownBanks = \App\Models\QuestionBank::whereIn('id', $bankIds)->withCount('items')->get();
                    $globalBanks = \App\Models\QuestionBank::where('is_visible_to_all', true)
                        ->whereNotIn('id', $bankIds)
                        ->withCount('items')
                        ->get();
                @endphp

                <p class="text-sm text-gray-400 mb-4">{{ __('Select a bank and enter how many random questions to pull:') }}</p>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @foreach($ownBanks as $bank)
                        <div class="bg-surface-700 rounded-xl p-4 border border-white/5 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-white truncate">{{ $bank->name }}</p>
                                <p class="text-xs text-gray-500">{{ $bank->items_count }} {{ __('questions') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <input type="number" name="bank_pulls[{{ $bank->id }}]" value="0" min="0" max="{{ $bank->items_count }}"
                                       class="w-20 input-dashboard text-center text-sm" placeholder="0">
                                <span class="text-xs text-gray-500">{{ __('random') }}</span>
                            </div>
                        </div>
                    @endforeach

                    @foreach($globalBanks as $bank)
                        <div class="bg-surface-700/50 rounded-xl p-4 border border-white/5 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-white truncate">{{ $bank->name }}</p>
                                <p class="text-xs text-gray-500">{{ $bank->items_count }} {{ __('questions') }} &middot; {{ __('Shared') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <input type="number" name="bank_pulls[{{ $bank->id }}]" value="0" min="0" max="{{ $bank->items_count }}"
                                       class="w-20 input-dashboard text-center text-sm" placeholder="0">
                                <span class="text-xs text-gray-500">{{ __('random') }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($ownBanks->isEmpty() && $globalBanks->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">{{ __('No question banks available.') }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Update Quiz') }}
                </button>
                <a href="{{ route('courses.quizzes.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let questionIndex = {{ count($quiz->questions) }};

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const idx = questionIndex++;
            const template = `
            <div class="question-item bg-surface-700 rounded-xl p-4 border border-white/5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-300 question-number">Q${idx + 1}</span>
                    <button type="button" onclick="this.closest('.question-item').remove(); updateQuestionNumbers();"
                            class="text-red-400 hover:text-red-300 text-sm transition-colors">{{ __('Remove') }}</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Question Type') }}</label>
                        <select name="questions[${idx}][type]" onchange="toggleOptions(this)"
                                class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                            <option value="multiple_choice">{{ __('Multiple Choice') }}</option>
                            <option value="true_false">{{ __('True/False') }}</option>
                            <option value="short_answer">{{ __('Short Answer') }}</option>
                            <option value="long_answer">{{ __('Long Answer') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Points') }}</label>
                        <input type="number" name="questions[${idx}][points]" value="10" min="1"
                               class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Question') }}</label>
                    <textarea name="questions[${idx}][question]" rows="2" required
                              class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500 resize-none"
                              placeholder="{{ __('Enter your question...') }}"></textarea>
                </div>
                <div class="options-container space-y-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Options') }}</label>
                    ${[0,1,2,3].map(o => `
                        <div class="flex items-center gap-2">
                            <input type="text" name="questions[${idx}][options][]"
                                   class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                   placeholder="{{ __('Option') }} ${String.fromCharCode(65 + o)}">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                                <input type="radio" name="questions[${idx}][correct_answer]" value="${o}" class="accent-brand-500">
                                {{ __('Correct') }}
                            </label>
                        </div>
                    `).join('')}
                </div>
                <div class="true-false-container hidden space-y-2 mt-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Correct Answer') }}</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="questions[${idx}][correct_answer]" value="true" class="accent-brand-500">
                            {{ __('True') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="questions[${idx}][correct_answer]" value="false" class="accent-brand-500">
                            {{ __('False') }}
                        </label>
                    </div>
                </div>
                <div class="short-answer-container hidden mt-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Correct Answer') }}</label>
                    <input type="text" name="questions[${idx}][correct_answer]"
                           class="w-full bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500"
                           placeholder="{{ __('Expected answer...') }}">
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', template);
        }

        function toggleOptions(select) {
            const item = select.closest('.question-item');
            const optionsContainer = item.querySelector('.options-container');
            const tfContainer = item.querySelector('.true-false-container');
            const saContainer = item.querySelector('.short-answer-container');
            optionsContainer.classList.toggle('hidden', select.value !== 'multiple_choice');
            tfContainer.classList.toggle('hidden', select.value !== 'true_false');
            saContainer.classList.toggle('hidden', select.value !== 'short_answer');
        }

        function updateQuestionNumbers() {
            document.querySelectorAll('.question-item').forEach((el, i) => {
                el.querySelector('.question-number').textContent = `Q${i + 1}`;
            });
        }
    </script>
    @endpush
</x-layouts.dashboard>
