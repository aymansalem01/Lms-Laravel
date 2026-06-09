<x-layouts.dashboard>
    <x-slot name="title">{{ __('Assignments') }} - {{ $course->name }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $course->name }} — {{ __('Assignments') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ count($assignments) }} {{ __('assignments') }}</p>
        </div>
        @if(auth()->user()->role === 'instructor')
            <a href="{{ route('courses.assignments.create', $course) }}"
               class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Create Assignment') }}
            </a>
        @endif
    </div>

    @if(count($assignments) === 0)
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <p class="text-gray-400 text-lg mb-2">{{ __('No assignments yet') }}</p>
            <p class="text-gray-500 text-sm">{{ __('Assignments will appear here once created.') }}</p>
            @if(auth()->user()->role === 'instructor')
                <a href="{{ route('courses.assignments.create', $course) }}" class="inline-block mt-4 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Create the first assignment') }}
                </a>
            @endif
        </div>
    @else
        <div class="grid gap-4">
            @foreach($assignments as $assignment)
                @php
                    $isOverdue = $assignment->due_date && now()->gt($assignment->due_date);
                    $submission = $assignment->submissions->where('student_id', auth()->id())->first();
                    $submissionsCount = $assignment->submissions->count();
                @endphp
                <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}"
                   class="bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/50 transition-all duration-200 group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-white font-semibold text-lg group-hover:text-brand-300 transition-colors">{{ $assignment->title }}</h3>
                                @if($isOverdue)
                                    <span class="text-[11px] font-semibold text-red-400 bg-red-500/10 px-2 py-0.5 rounded-md">{{ __('Overdue') }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-400 line-clamp-2 mb-3">{{ $assignment->description }}</p>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                @if($assignment->file_path)
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span class="text-gray-500">{{ __('Attachment') }}</span>
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $assignment->due_date ? $assignment->due_date->format('M d, Y h:i A') : __('No due date') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $assignment->max_score }} {{ __('points') }}
                                </span>
                                @if(auth()->user()->role === 'instructor')
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $submissionsCount }}/{{ $course->students->count() }} {{ __('submitted') }}
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5">
                                        @if($submission)
                                            @if($submission->grade)
                                                <span class="text-green-400 font-medium">{{ number_format($submission->grade->score, 1) }}/{{ $assignment->max_score }}</span>
                                            @else
                                                <span class="text-yellow-400">{{ __('Submitted') }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">{{ __('Not submitted') }}</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-400 shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.dashboard>
