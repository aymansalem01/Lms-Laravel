<x-layouts.dashboard>
    <x-slot name="title">{{ __('All Students') }}</x-slot>

    <div class="mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('All Students') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('All courses and their enrolled students.') }}</p>
        </div>
    </div>

    @forelse($courses as $course)
        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-surface-700">
                <h2 class="text-lg font-semibold text-white">{{ $course->title }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Instructor') }}: {{ $course->instructor->name ?? '&mdash;' }} &middot; {{ $course->students->count() }} {{ __('enrolled') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-surface-700">
                            <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Student') }}</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Program') }}</th>
                            <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Enrolled Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-700">
                        @forelse($course->students as $student)
                            <tr class="hover:bg-surface-700/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-white">{{ $student->name ?? __('Unknown') }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-400">{{ $student->email ?? '&mdash;' }}</td>
                                <td class="px-5 py-3.5">
                                    @if($student->program ?? false)
                                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400">{{ $student->program }}</span>
                                    @else
                                        <span class="text-sm text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-400">
                                    {{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y') : '&mdash;' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <p class="text-gray-400 text-sm">{{ __('No students enrolled in this course.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-12 text-center">
            <p class="text-gray-400 text-sm">{{ __('No courses found.') }}</p>
        </div>
    @endforelse
</x-layouts.dashboard>
