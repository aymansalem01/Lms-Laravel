<x-layouts.dashboard>
    <x-slot name="title">{{ __('My Progress') }} - {{ $course->title }}</x-slot>

    @php
        $viewingInstructor = isset($students) && auth()->user()->isInstructorOrAdmin();
    @endphp

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ $viewingInstructor ? __('Course-wide student progress') : __('Your learning progress and grades.') }}</p>
            </div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">{{ __('Back to Course') }} &rarr;</a>
        </div>
    </div>

    @if($viewingInstructor)
        @php
            $courseGrades = $allGrades ?? collect();
            $studentsList = $students ?? collect();

            $studentData = $studentsList->map(function($s) use ($courseGrades, $course) {
                $sGrades = $courseGrades->get($s->id, collect());
                $avg = $sGrades->avg('score');
                $overdue = $course->assignments->filter(function($a) use ($s) {
                    return $a->due_date && $a->due_date->isPast() && !$sGrades->contains(function($g) use ($a) {
                        return optional($g->submission)->assignment_id === $a->id;
                    });
                })->count();

                if ($avg === null) {
                    $risk = 'no_data';
                    $riskLabel = 'No Data';
                    $riskColor = 'bg-gray-500/10 text-gray-400';
                } elseif ($avg >= 85) {
                    $risk = 'excelling';
                    $riskLabel = 'Excelling';
                    $riskColor = 'bg-green-500/10 text-green-400';
                } elseif ($avg >= 70) {
                    $risk = 'on_track';
                    $riskLabel = 'On Track';
                    $riskColor = 'bg-blue-500/10 text-blue-400';
                } elseif ($avg >= 55 || $overdue >= 2) {
                    $risk = 'needs_attention';
                    $riskLabel = 'Needs Attention';
                    $riskColor = 'bg-yellow-500/10 text-yellow-400';
                } else {
                    $risk = 'at_risk';
                    $riskLabel = 'At Risk';
                    $riskColor = 'bg-red-500/10 text-red-400';
                }

                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'average' => $avg ? round($avg, 1) : null,
                    'overdue' => $overdue,
                    'risk' => $risk,
                    'riskLabel' => $riskLabel,
                    'riskColor' => $riskColor,
                    'graded' => $sGrades->count(),
                ];
            });

            $totalStudents = $studentData->count();
            $atRiskCount = $studentData->where('risk', 'at_risk')->count();
            $needsAttentionCount = $studentData->where('risk', 'needs_attention')->count();
            $excellingCount = $studentData->where('risk', 'excelling')->count();
            $onTrackCount = $studentData->where('risk', 'on_track')->count();
            $courseAverage = $studentData->whereNotNull('average')->avg('average');
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('Total Students') }}</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $totalStudents }}</p>
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('Course Average') }}</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $courseAverage ? round($courseAverage, 1) . '%' : 'N/A' }}</p>
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('Excelling') }}</p>
                <p class="text-2xl font-bold text-green-400 mt-1">{{ $excellingCount }}</p>
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('On Track') }}</p>
                <p class="text-2xl font-bold text-blue-400 mt-1">{{ $onTrackCount }}</p>
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('Needs Attention') }}</p>
                <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $needsAttentionCount }}</p>
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium">{{ __('At Risk') }}</p>
                <p class="text-2xl font-bold text-red-400 mt-1">{{ $atRiskCount }}</p>
            </div>
        </div>

        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-surface-700/50">
                            <th class="text-left px-5 py-3 text-gray-400 font-medium">{{ __('Student') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Average') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Graded') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Overdue') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Risk Level') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentData as $sd)
                            <tr class="border-b border-white/5 hover:bg-surface-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-xs font-bold text-brand-300">
                                            {{ strtoupper(substr($sd['name'], 0, 1)) }}
                                        </div>
                                        <span class="text-white font-medium">{{ $sd['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-center px-4 py-3 text-white">{{ $sd['average'] !== null ? $sd['average'] . '%' : '—' }}</td>
                                <td class="text-center px-4 py-3 text-gray-400">{{ $sd['graded'] }}</td>
                                <td class="text-center px-4 py-3">
                                    @if($sd['overdue'] > 0)
                                        <span class="text-red-400">{{ $sd['overdue'] }}</span>
                                    @else
                                        <span class="text-gray-500">0</span>
                                    @endif
                                </td>
                                <td class="text-center px-4 py-3">
                                    <span class="text-[11px] font-medium px-2.5 py-1 rounded-full {{ $sd['riskColor'] }}">
                                        {{ __($sd['riskLabel']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-500">{{ __('No students enrolled.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Student view --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-white">{{ __('Overall Progress') }}</h2>
                <span class="text-2xl font-bold gt">{{ $overallProgress ?? 0 }}%</span>
            </div>
            <div class="w-full h-3 bg-surface-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-brand-500 to-coral-500 rounded-full transition-all duration-500" style="width: {{ $overallProgress ?? 0 }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                <span>{{ $completedLessons ?? 0 }} / {{ $totalLessons ?? 0 }} {{ __('lessons completed') }}</span>
                <span>{{ $completedAssignments ?? 0 }} / {{ $totalAssignments ?? 0 }} {{ __('assignments submitted') }}</span>
            </div>
        </div>

        @php
            $gradeScores = $grades->pluck('score');
            $gradeAvg = $gradeScores->avg();
            $overdueCount = $course->assignments->filter(function($a) {
                return $a->due_date && $a->due_date->isPast() && !$grades->contains(function($g) use ($a) {
                    return optional($g->submission)->assignment_id === $a->id;
                });
            })->count();

            if ($gradeAvg === null) {
                $riskLabel = 'No Data';
                $riskColor = 'bg-gray-500/10 text-gray-400';
            } elseif ($gradeAvg >= 85) {
                $riskLabel = 'Excelling';
                $riskColor = 'bg-green-500/10 text-green-400';
            } elseif ($gradeAvg >= 70) {
                $riskLabel = 'On Track';
                $riskColor = 'bg-blue-500/10 text-blue-400';
            } elseif ($gradeAvg >= 55 || $overdueCount >= 2) {
                $riskLabel = 'Needs Attention';
                $riskColor = 'bg-yellow-500/10 text-yellow-400';
            } else {
                $riskLabel = 'At Risk';
                $riskColor = 'bg-red-500/10 text-red-400';
            }
        @endphp

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-sm font-semibold text-white">{{ __('Your Status') }}</h2>
                <span class="text-[11px] font-medium px-3 py-1 rounded-full {{ $riskColor }}">{{ __($riskLabel) }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                {{ __('Average grade') }}: <span class="text-white font-medium">{{ $gradeAvg ? round($gradeAvg, 1) . '%' : '—' }}</span>
                &middot;
                {{ __('Overdue assignments') }}: <span class="text-white font-medium">{{ $overdueCount }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Course Content') }}</h2>
                <div class="space-y-3">
                    @forelse(($course->modules ?? collect()) as $module)
                        <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left transition-colors hover:bg-surface-700/50">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold {{ $module->is_completed ? 'bg-green-500/20 text-green-400' : 'bg-surface-700 text-gray-500' }}">
                                        {{ $module->sort_order ?? $loop->iteration }}
                                    </span>
                                    <span class="text-sm font-medium text-white truncate">{{ $module->title }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($module->lessons_count ?? 0)
                                        <span class="text-xs text-gray-500">{{ $module->completed_lessons_count ?? 0 }}/{{ $module->lessons_count }}</span>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div class="border-t border-surface-700 px-4 py-2 space-y-1">
                                    @forelse(($module->lessons ?? collect()) as $lesson)
                                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ $lesson->is_completed ? 'text-gray-400' : 'text-gray-300' }}">
                                            @if($lesson->is_completed)
                                                <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                            <a href="{{ route('courses.content.lesson-show', [$course, $lesson]) }}" class="hover:text-brand-400 transition-colors">{{ $lesson->title }}</a>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-600 px-3 py-2">{{ __('No lessons.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-surface-800 border border-white/10 rounded-xl p-6 text-center">
                            <p class="text-gray-500 text-sm">{{ __('No modules yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Assignments & Grades') }}</h2>
                <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                    @forelse(($assignments ?? $course->assignments ?? collect()) as $assignment)
                        <div class="px-4 py-4 {{ $loop->first ? '' : 'border-t border-surface-700' }}">
                            <div class="flex items-center justify-between mb-1">
                                <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $assignment->title }}</a>
                                @if($assignment->grade)
                                    <span class="text-sm font-bold {{ $assignment->grade->score >= 80 ? 'text-green-400' : ($assignment->grade->score >= 60 ? 'text-coral-400' : 'text-red-400') }}">
                                        {{ $assignment->grade->score }}/{{ $assignment->max_score ?? 100 }}
                                    </span>
                                @elseif($assignment->submitted)
                                    <span class="text-xs text-yellow-400">{{ __('Pending') }}</span>
                                @else
                                    <span class="text-xs text-gray-500">{{ __('Not submitted') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span>{{ __('Due') }}: {{ $assignment->due_at?->format('M d, Y') ?? __('No due date') }}</span>
                                @if($assignment->grade)
                                    <span>{{ __('Graded') }}: {{ $assignment->grade->created_at ? $assignment->grade->created_at->format('M d, Y') : __('Pending') }}</span>
                                @endif
                            </div>
                            @if($assignment->grade)
                                <div class="mt-2 w-full h-1.5 bg-surface-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $assignment->grade->score >= 80 ? 'bg-green-500' : ($assignment->grade->score >= 60 ? 'bg-coral-500' : 'bg-red-500') }}"
                                         style="width: {{ ($assignment->grade->score / ($assignment->max_score ?? 100)) * 100 }}%">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-gray-500 text-sm">{{ __('No assignments yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</x-layouts.dashboard>
