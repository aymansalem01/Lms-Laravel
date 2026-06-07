@props(['assignment', 'course', 'students'])

@php
    $rubric = $assignment->rubric;
    $hasRubric = $rubric && $rubric->criteria && $rubric->levels;
    $maxScore = $assignment->max_score ?? 100;
@endphp

<div x-data="gradebookGrader({{ $students->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'submission_id' => $s->submissions->first()?->id, 'has_submission' => $s->submissions->isNotEmpty(), 'existing_score' => $s->submissions->first()?->grade?->score ?? '', 'existing_feedback' => $s->submissions->first()?->grade?->feedback ?? '']) }}, {{ $maxScore }}, @js($hasRubric ? ['criteria' => $rubric->criteria, 'levels' => $rubric->levels, 'cells' => $rubric->cells] : null))"
     x-cloak
     @open-grader.window="openForStudent($event.detail.studentId, $event.detail.studentName)">
    <template x-if="open">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="open = false">
            <div class="fixed inset-0 bg-black/60" @click="open = false"></div>
            <div class="relative bg-surface-800 border border-white/10 rounded-2xl p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white" x-text="studentName"></h3>
                        <p class="text-sm text-gray-400">{{ $assignment->title }}</p>
                    </div>
                    <button @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <template x-if="!hasSubmission">
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl px-4 py-3 mb-4">
                        <p class="text-sm text-yellow-400 font-medium">{{ __('This student has not submitted yet. You can assign a manual grade.') }}</p>
                    </div>
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Score') }} (0 - <span x-text="maxScore"></span>)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" x-model.number="score" :min="0" :max="maxScore" step="0.1"
                                   class="flex-1 accent-brand-500">
                            <input type="number" x-model.number="score" :min="0" :max="maxScore" step="0.1"
                                   class="w-20 bg-surface-700 border border-white/10 text-white rounded-lg py-2 px-3 text-sm focus:outline-none focus:border-brand-500 text-center">
                        </div>
                    </div>

                    <template x-if="rubric">
                        <div class="bg-surface-700 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-white mb-3">{{ __('Rubric Criteria') }}</h4>
                            <div class="space-y-3">
                                <template x-for="(criterion, ci) in rubric.criteria" :key="ci">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1.5" x-text="criterion.name"></p>
                                        <div class="flex gap-1.5">
                                            <template x-for="(level, li) in rubric.levels" :key="li">
                                                <button type="button"
                                                        @click="selectLevel(ci, li)"
                                                        :class="selectedLevel(ci, li) ? 'bg-brand-600 text-white border-brand-500' : 'bg-surface-800 text-gray-400 border-white/10 hover:border-brand-500/50'"
                                                        class="flex-1 text-center border rounded-lg px-2 py-1.5 text-xs font-medium transition-colors">
                                                    <span x-text="rubric.cells?.[ci]?.[li] ?? 0"></span>
                                                    <span class="block text-[10px] opacity-70" x-text="level.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <p class="text-sm text-gray-300 mt-3">{{ __('Rubric Total') }}: <span class="text-brand-400 font-semibold" x-text="rubricTotal.toFixed(1)"></span></p>
                        </div>
                    </template>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Feedback') }}</label>
                        <textarea x-model="feedback" rows="4"
                                  class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 resize-none"
                                  placeholder="{{ __('Write feedback...') }}"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-white/10">
                    <button type="button" @click="saveDraft"
                            class="text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 font-medium rounded-xl px-5 py-2 transition-colors">
                        {{ __('Save Draft') }}
                    </button>
                    <button type="button" @click="releaseGrade"
                            class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-5 py-2 transition-colors">
                        {{ __('Release') }}
                    </button>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        function gradebookGrader(studentsData, maxScore, rubricData) {
            return {
                open: false,
                studentId: null,
                studentName: '',
                hasSubmission: false,
                score: 0,
                feedback: '',
                rubric: rubricData,
                selectedRubric: {},
                rubricTotal: 0,

                openForStudent(id, name) {
                    const student = studentsData.find(s => s.id === id);
                    if (!student) return;
                    this.studentId = id;
                    this.studentName = name;
                    this.hasSubmission = student.has_submission;
                    this.score = student.existing_score || 0;
                    this.feedback = student.existing_feedback || '';
                    this.selectedRubric = {};
                    this.rubricTotal = 0;
                    this.open = true;
                },

                selectLevel(ci, li) {
                    const score = this.rubric.cells?.[ci]?.[li] ?? 0;
                    if (this.selectedRubric[ci] === li) {
                        delete this.selectedRubric[ci];
                    } else {
                        this.selectedRubric[ci] = li;
                    }
                    this.recalcRubricTotal();
                },

                selectedLevel(ci, li) {
                    return this.selectedRubric[ci] === li;
                },

                recalcRubricTotal() {
                    let total = 0;
                    for (const ci in this.selectedRubric) {
                        const li = this.selectedRubric[ci];
                        total += parseFloat(this.rubric.cells?.[ci]?.[li] ?? 0);
                    }
                    this.rubricTotal = total;
                    if (this.rubric) {
                        this.score = total;
                    }
                },

                saveDraft() {
                    this.submitGrade(false);
                },

                releaseGrade() {
                    this.submitGrade(true);
                },

                submitGrade(release) {
                    const url = release
                        ? '{{ route("courses.assignments.direct-grade", [$course, $assignment, "__student__"]) }}'.replace('__student__', this.studentId)
                        : '{{ route("courses.assignments.direct-grade", [$course, $assignment, "__student__"]) }}'.replace('__student__', this.studentId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            score: this.score,
                            feedback: this.feedback,
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.open = false;
                            window.location.reload();
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</div>
