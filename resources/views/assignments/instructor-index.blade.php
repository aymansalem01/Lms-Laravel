<x-layouts.dashboard>
    <x-slot name="title">{{ __('Assignments') }}</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Course Assignments') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Overview of all assignments across your courses') }}</p>
    </div>

    @if($assignments->isEmpty())
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <p class="text-gray-400 text-lg mb-2">{{ __('No assignments yet') }}</p>
            <p class="text-gray-500 text-sm">{{ __('Create assignments in your course content pages.') }}</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($assignments as $courseTitle => $courseAssignments)
                <div>
                    <h2 class="text-lg font-semibold text-white mb-3">{{ $courseTitle }}</h2>
                    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider border-b border-white/10">
                                    <th class="px-5 py-3">{{ __('Assignment') }}</th>
                                    <th class="px-5 py-3">{{ __('Submissions') }}</th>
                                    <th class="px-5 py-3">{{ __('Pending') }}</th>
                                    <th class="px-5 py-3">{{ __('Graded') }}</th>
                                    <th class="px-5 py-3">{{ __('Due Date') }}</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($courseAssignments as $assignment)
                                    @php
                                        $totalSubs = $assignment->submissions->count();
                                        $gradedSubs = $assignment->submissions->filter(fn($s) => $s->grade)->count();
                                        $pendingSubs = $totalSubs - $gradedSubs;
                                    @endphp
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <span class="text-white font-medium">{{ $assignment->title }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-gray-400">{{ $totalSubs }} / {{ $assignment->course->students->count() ?? '—' }}</td>
                                        <td class="px-5 py-4">
                                            @if($pendingSubs > 0)
                                                <span class="text-yellow-400 font-medium">{{ $pendingSubs }}</span>
                                            @else
                                                <span class="text-gray-500">0</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($gradedSubs > 0)
                                                <span class="text-green-400 font-medium">{{ $gradedSubs }}</span>
                                            @else
                                                <span class="text-gray-500">0</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-gray-400">
                                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('courses.assignments.show', [$assignment->course, $assignment]) }}" class="text-brand-400 hover:text-brand-300 transition-colors text-sm font-medium">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.dashboard>
