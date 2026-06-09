<x-layouts.dashboard>
    <x-slot name="title">{{ __('Students') }}</x-slot>

    <div class="mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Students') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('All courses and their enrolled students.') }}</p>
        </div>
    </div>

    @forelse($courses as $course)
        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-surface-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $course->title }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $course->instructor->name ?? '—' }} &middot; {{ $course->students->count() }} {{ __('enrolled') }}</p>
                    </div>
                    <a href="{{ route('courses.roster', $course) }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">{{ __('View Students') }} &rarr;</a>
                </div>
            </div>
            <div class="p-5">
                @forelse($course->students->take(10) as $student)
                    <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-surface-700/50' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $student->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $student->email }}</p>
                        </div>
                        @if($student->program ?? false)
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400 shrink-0">{{ $student->program }}</span>
                        @endif
                        <span class="text-xs text-gray-500 shrink-0">{{ $student->pivot->enrolled_at?->format('M d, Y') ?? '—' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No students enrolled yet.') }}</p>
                @endforelse
                @if($course->students->count() > 10)
                    <div class="text-center pt-3">
                        <a href="{{ route('courses.roster', $course) }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">{{ __('View all') }} {{ $course->students->count() }} {{ __('students') }} &rarr;</a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-12 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-surface-700 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
            </div>
            <p class="text-gray-400 text-sm">{{ __('No courses found.') }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ __('You are not assigned to any courses yet.') }}</p>
        </div>
    @endforelse
</x-layouts.dashboard>
