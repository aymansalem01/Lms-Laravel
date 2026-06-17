<x-layouts.dashboard>
    <x-slot name="title">{{ __('Quiz Review') }} — {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to quiz') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ __('Quiz Review') }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Total Submissions') }}</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $totalSubmissions }} / {{ $students->count() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Graded') }}</p>
            <p class="text-2xl font-bold text-green-400 mt-1">{{ $gradedCount }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Questions') }}</p>
            <p class="text-2xl font-bold text-brand-400 mt-1">{{ $quiz->questions_count }}</p>
        </div>
    </div>

    @php
        $manualQuestions = $quiz->questions->filter(fn($q) => in_array($q->type, ['long_answer', 'short_answer']));
    @endphp

    <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 text-xs uppercase tracking-wider border-b border-white/10">
                        <th class="px-5 py-3">{{ __('Student') }}</th>
                        <th class="px-5 py-3">{{ __('Status') }}</th>
                        <th class="px-5 py-3">{{ __('Auto') }}</th>
                        <th class="px-5 py-3">{{ __('Manual') }}</th>
                        <th class="px-5 py-3">{{ __('Total') }}</th>
                        <th class="px-5 py-3">{{ __('Attempts') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($students as $student)
                        @php
                            $attempts = $student->quizAttempts;
                            $attempt = $attempts->sortByDesc('created_at')->first();
                            $released = $attempt && $attempt->released_at;
                            $status = match(true) {
                                $attempts->isEmpty() => 'not_submitted',
                                (bool) $released => 'graded',
                                default => 'submitted',
                            };
                            $representative = match($quiz->grading_method) {
                                'min' => $attempts->sortBy('score')->first(),
                                'last' => $attempts->sortByDesc('created_at')->first(),
                                'first' => $attempts->sortBy('created_at')->first(),
                                'avg' => $attempts->sortByDesc('created_at')->first(),
                                default => $attempts->sortByDesc('score')->first(),
                            };
                            $computedScore = match($quiz->grading_method) {
                                'min' => $attempts->min('score'),
                                'last' => $attempts->sortByDesc('created_at')->first()?->score,
                                'first' => $attempts->sortBy('created_at')->first()?->score,
                                'avg' => $attempts->avg('score'),
                                default => $attempts->max('score'),
                            };
                            $needsManual = $attempt && $manualQuestions->isNotEmpty();
                            $manualSum = $attempt ? $attempt->manualScoreSum() : 0;
                            $autoScore = $attempt ? ((float) $attempt->score - $manualSum) : 0;
                        @endphp
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-white">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($status === 'not_submitted')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-gray-500/10 text-gray-400">{{ __('Not Submitted') }}</span>
                                @elseif($status === 'graded')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-green-500/10 text-green-400">{{ __('Graded') }}</span>
                                @else
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400">{{ __('Submitted') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-300">{{ $attempt ? number_format(max($autoScore, 0), 1) : '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($needsManual)
                                    @if($attempt->manual_scores)
                                        <span class="text-green-400 font-medium">{{ number_format($manualSum, 1) }}</span>
                                    @else
                                        <span class="text-yellow-400 font-medium">{{ __('Pending') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($computedScore !== null)
                                    <span class="text-gray-300 font-medium">{{ number_format($computedScore, 1) }} / {{ number_format($attempt?->max_score ?? 0, 1) }}</span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($attempts->isNotEmpty())
                                    <span class="text-gray-400">{{ $attempts->count() }}</span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($needsManual && $attempt)
                                        <button type="button" onclick="openManualGrading({{ $attempt->id }})"
                                                class="text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 font-medium rounded-xl px-3 py-1.5 transition-colors duration-200 inline-block">
                                            {{ $attempt->manual_scores ? __('Re-grade') : __('Grade') }}
                                        </button>
                                    @endif
                                    @if($representative)
                                        <a href="{{ route('courses.quizzes.results', [$course, $quiz, $representative]) }}"
                                           class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-medium rounded-xl px-3 py-1.5 transition-colors duration-200 inline-block">
                                            {{ __('View') }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-600">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="text-gray-400 text-sm">{{ __('No students enrolled.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Manual Grading Modal --}}
    <div id="manual-grade-modal" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" style="display:none">
        <div class="bg-surface-800 border border-white/10 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <form id="manual-grade-form" method="POST">
                @csrf
                <div class="p-6 border-b border-white/10 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">{{ __('Manual Grading') }}</h2>
                    <button type="button" onclick="closeManualGrading()" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6" id="manual-grade-questions">
                    {{-- populated by JS --}}
                </div>
                <div class="p-6 border-t border-white/10 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeManualGrading()" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2">{{ __('Cancel') }}</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2 text-sm transition-colors">{{ __('Save Grades') }}</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const manualQuestions = @json($manualQuestions->map(fn($q) => ['id' => $q->id, 'question' => $q->question, 'points' => $q->points, 'type' => $q->type]));
        const allAttempts = @json($students->pluck('quizAttempts')->flatten()->keyBy('id')->map(fn($a) => [
            'id' => $a->id,
            'answers' => $a->answers,
            'manual_scores' => $a->manual_scores,
        ]));

        function openManualGrading(attemptId) {
            const attempt = allAttempts[attemptId];
            if (!attempt) return;

            const form = document.getElementById('manual-grade-form');
            form.action = '{{ route('courses.quizzes.grade-manual', [$course, $quiz, '__ATTEMPT__']) }}'.replace('__ATTEMPT__', attemptId);

            const container = document.getElementById('manual-grade-questions');
            container.innerHTML = '';

            manualQuestions.forEach(q => {
                const answer = attempt.answers?.[q.id] ?? '';
                const savedScore = attempt.manual_scores?.[q.id] ?? '';

                const div = document.createElement('div');
                div.className = 'bg-surface-700 rounded-xl p-4 space-y-3';
                div.innerHTML = `
                    <div class="flex items-start justify-between gap-4">
                        <p class="text-sm text-white font-medium flex-1">${esc(q.question)}</p>
                        <span class="text-xs text-gray-500 shrink-0">${q.points} pts</span>
                    </div>
                    <div class="bg-surface-800 rounded-lg px-3 py-2 text-sm text-gray-300 whitespace-pre-wrap max-h-40 overflow-y-auto">${esc(answer || '(no answer)')}</div>
                    <div class="flex items-center gap-3">
                        <label class="text-xs text-gray-500 shrink-0">{{ __('Score') }}:</label>
                        <input type="number" name="scores[${q.id}]" value="${savedScore}" min="0" max="${q.points}" step="0.5"
                               class="w-24 bg-surface-800 border border-white/10 text-white rounded-lg py-1.5 px-3 text-sm focus:outline-none focus:border-brand-500">
                        <span class="text-xs text-gray-500">/ ${q.points}</span>
                    </div>
                `;
                container.appendChild(div);
            });

            document.getElementById('manual-grade-modal').style.display = 'flex';
        }

        function closeManualGrading() {
            document.getElementById('manual-grade-modal').style.display = 'none';
        }

        function esc(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
    @endpush
</x-layouts.dashboard>
