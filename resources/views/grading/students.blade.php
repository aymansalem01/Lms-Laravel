<x-layouts.dashboard>
    <x-slot name="title">{{ $assignment->title }} — {{ $course->title }} — {{ __('Grading') }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('grading.assignments', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to assignments
        </a>
        <div class="flex items-center justify-between mt-1">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $assignment->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $course->title }} &middot; Max score: {{ $assignment->max_score ?? 100 }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('grading.export-assignment', [$course, $assignment]) }}"
                   class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Marks CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Enrolled Students</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $students->count() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Graded</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $gradedCount }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Average Score</p>
            <p class="text-2xl font-bold text-brand-400 mt-1">{{ $averageScore ? number_format($averageScore, 1) : '—' }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Student</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Email</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Score</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Feedback</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($students as $student)
                        @php
                            $submission = $student->submissions->first();
                            $grade = $submission?->grade;
                            $status = match(true) {
                                !$submission => 'not_submitted',
                                (bool)$grade => 'graded',
                                default => 'submitted',
                            };
                        @endphp
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <span class="text-white font-medium">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $student->email }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($status === 'not_submitted')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-gray-500/10 text-gray-400">Not Submitted</span>
                                @elseif($status === 'graded')
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Graded</span>
                                @else
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-400">Submitted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($grade)
                                    <span class="font-semibold {{ (int)$grade->score < 50 ? 'text-red-400' : ((int)$grade->score < 70 ? 'text-amber-400' : 'text-emerald-400') }}">
                                        {{ number_format($grade->score, 1) }}/{{ $assignment->max_score ?? 100 }}
                                    </span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-sm max-w-xs truncate">{{ $grade?->feedback ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        x-data
                                        @click="$dispatch('open-grader', { studentId: {{ $student->id }}, studentName: '{{ addslashes($student->name) }}' })"
                                        class="text-xs bg-brand-600 hover:bg-brand-500 text-white font-medium rounded-lg px-3 py-1.5 transition-colors">
                                    Grade
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No students enrolled in this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-gradebook-modal :assignment="$assignment" :course="$course" :students="$students" />
</x-layouts.dashboard>
