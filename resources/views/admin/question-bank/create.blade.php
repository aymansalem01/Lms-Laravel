<x-layouts.dashboard>
    <x-slot name="title">Create Question Bank &mdash; SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.question-bank.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Banks
        </a>
        <h1 class="text-2xl font-bold text-white">Create Question Bank</h1>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.question-bank.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Bank Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="input-dashboard" placeholder="e.g. Midterm Exam Bank">
                </div>

                @php $courseOptions = $courses->map(fn($c) => ['id' => (string)$c->id, 'label' => $c->title])->values(); @endphp
                <div x-data="{
                    search: '',
                    open: false,
                    selected: {{ json_encode(array_map('strval', old('course_ids', []))) }},
                    items: {{ json_encode($courseOptions) }},
                    get filtered() {
                        return this.items.filter(c => c.label.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }" class="relative">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Courses</label>
                    <input type="text" x-model="search" @click="open = true" @input="open = true"
                           placeholder="Search courses..."
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 mb-2">
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute z-10 mt-1 w-full bg-surface-700 border border-surface-600 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                        <template x-for="course in filtered" :key="course.id">
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-600 cursor-pointer border-b border-surface-600 last:border-0">
                                <input type="checkbox" x-model="selected" :value="course.id"
                                       class="rounded bg-surface-600 border-surface-500 text-brand-500 focus:ring-brand-500/50">
                                <span class="text-sm text-gray-200" x-text="course.label"></span>
                            </label>
                        </template>
                        <p x-show="filtered.length === 0" class="text-sm text-gray-500 px-3 py-4 text-center">No courses found.</p>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-2" x-show="selected.length > 0">
                        <template x-for="id in selected" :key="id">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-400 text-xs">
                                <span x-text="items.find(c => c.id === id)?.label"></span>
                                <button @click="selected = selected.filter(s => s !== id)" type="button" class="hover:text-white">&times;</button>
                            </span>
                        </template>
                    </div>
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="course_ids[]" :value="id">
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_visible_to_all" value="1" id="is_visible_to_all" class="accent-brand-500"
                        {{ old('is_visible_to_all') ? 'checked' : '' }}>
                    <label for="is_visible_to_all" class="text-sm text-gray-400">Share with all courses (global bank)</label>
                </div>
            </div>

            <div id="questions-container" class="space-y-4">
                <div class="question-item bg-surface-800 border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-300 question-number">Q1</span>
                        <button type="button" onclick="this.closest('.question-item').remove(); updateQuestionNumbers();"
                                class="text-red-400 hover:text-red-300 text-sm transition-colors hidden">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Type</label>
                            <select name="questions[0][type]" onchange="toggleOptions(this)"
                                    class="input-dashboard">
                                <option value="multiple_choice" {{ old('questions.0.type') === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('questions.0.type') === 'true_false' ? 'selected' : '' }}>True/False</option>
                                <option value="short_answer" {{ old('questions.0.type') === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                <option value="long_answer" {{ old('questions.0.type') === 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Points</label>
                            <input type="number" name="questions[0][points]" value="{{ old('questions.0.points', 10) }}" min="1"
                                   class="input-dashboard">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Question</label>
                        <textarea name="questions[0][question]" rows="2" required
                                  class="input-dashboard resize-none"
                                  placeholder="Enter your question...">{{ old('questions.0.question') }}</textarea>
                    </div>
                    <div class="options-container space-y-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Options</label>
                        @foreach([0,1,2,3] as $opt)
                        <div class="flex items-center gap-2">
                            <input type="text" name="questions[0][options][]"
                                   class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500"
                                   placeholder="Option {{ chr(65 + $opt) }}" value="{{ old("questions.0.options.$opt") }}">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                                <input type="radio" name="questions[0][correct_answer]" value="{{ $opt }}" class="accent-brand-500" {{ old('questions.0.correct_answer') == $opt ? 'checked' : '' }}>
                                Correct
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="true-false-container hidden space-y-2 mt-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-300">
                                <input type="radio" name="questions[0][correct_answer]" value="true" class="accent-brand-500" {{ old('questions.0.correct_answer') === 'true' ? 'checked' : '' }}>
                                True
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-300">
                                <input type="radio" name="questions[0][correct_answer]" value="false" class="accent-brand-500" {{ old('questions.0.correct_answer') === 'false' ? 'checked' : '' }}>
                                False
                            </label>
                        </div>
                    </div>
                    <div class="short-answer-container hidden mt-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                        <input type="text" name="questions[0][correct_answer]"
                               class="input-dashboard" value="{{ old('questions.0.correct_answer') }}"
                               placeholder="Expected answer...">
                    </div>
                </div>
            </div>

            <button type="button" onclick="addQuestion()"
                    class="w-full py-3 border-2 border-dashed border-white/10 rounded-xl text-sm text-gray-500 hover:text-brand-400 hover:border-brand-500/50 transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Another Question
            </button>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    Create Bank
                </button>
                <a href="{{ route('admin.question-bank.index') }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let questionIndex = 1;

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const idx = questionIndex++;
            const template = `
            <div class="question-item bg-surface-800 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-300 question-number">Q${idx + 1}</span>
                    <button type="button" onclick="this.closest('.question-item').remove(); updateQuestionNumbers();"
                            class="text-red-400 hover:text-red-300 text-sm transition-colors">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Type</label>
                        <select name="questions[${idx}][type]" onchange="toggleOptions(this)"
                                class="input-dashboard">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="long_answer">Long Answer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Points</label>
                        <input type="number" name="questions[${idx}][points]" value="10" min="1"
                               class="input-dashboard">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Question</label>
                    <textarea name="questions[${idx}][question]" rows="2" required
                              class="input-dashboard resize-none"
                              placeholder="Enter your question..."></textarea>
                </div>
                <div class="options-container space-y-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Options</label>
                    ${[0,1,2,3].map(o => `
                        <div class="flex items-center gap-2">
                            <input type="text" name="questions[${idx}][options][]"
                                   class="flex-1 bg-surface-800 border border-white/10 text-white rounded-xl py-2.5 px-3 text-sm focus:outline-none focus:border-brand-500"
                                   placeholder="Option ${String.fromCharCode(65 + o)}">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 shrink-0">
                                <input type="radio" name="questions[${idx}][correct_answer]" value="${o}" class="accent-brand-500">
                                Correct
                            </label>
                        </div>
                    `).join('')}
                </div>
                <div class="true-false-container hidden space-y-2 mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="questions[${idx}][correct_answer]" value="true" class="accent-brand-500">
                            True
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="radio" name="questions[${idx}][correct_answer]" value="false" class="accent-brand-500">
                            False
                        </label>
                    </div>
                </div>
                <div class="short-answer-container hidden mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Correct Answer</label>
                    <input type="text" name="questions[${idx}][correct_answer]"
                           class="input-dashboard"
                           placeholder="Expected answer...">
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

        document.querySelectorAll('.question-item select[name$="[type]"]').forEach(s => toggleOptions(s));
    </script>
    @endpush
</x-layouts.dashboard>