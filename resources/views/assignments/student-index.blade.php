<x-layouts.dashboard>
    <x-slot name="title">{{ __('My Assignments') }}</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('My Assignments') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Track and manage all your assignments across courses') }}</p>
    </div>

    @php
        $overdue = [];
        $pending = [];
        $submitted = [];
        $graded = [];

        foreach ($assignments as $assignment) {
            $submission = $assignment->submissions->where('student_id', auth()->id())->first();
            $isOverdue = $assignment->due_date && now()->gt($assignment->due_date);

            if ($submission && $submission->grade && $submission->grade->is_published) {
                $graded[] = $assignment;
            } elseif ($submission) {
                $submitted[] = $assignment;
            } elseif ($isOverdue) {
                $overdue[] = $assignment;
            } else {
                $pending[] = $assignment;
            }
        }
    @endphp

    @if(count($overdue) + count($pending) + count($submitted) + count($graded) === 0)
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <p class="text-gray-400 text-lg mb-2">{{ __('No assignments yet') }}</p>
            <p class="text-gray-500 text-sm">{{ __('When your instructors create assignments, they will appear here.') }}</p>
        </div>
    @else
        <div class="space-y-8">
            {{-- Overdue --}}
            @if(count($overdue) > 0)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Overdue') }}</h2>
                        <span class="text-xs bg-red-500/10 text-red-400 font-semibold px-2 py-0.5 rounded-md">{{ count($overdue) }}</span>
                    </div>
                    <div class="grid gap-3">
                        @foreach($overdue as $assignment)
                            <a href="{{ route('courses.assignments.show', [$assignment->course, $assignment]) }}"
                               class="bg-surface-800 border border-red-500/20 rounded-xl p-4 hover:border-red-500/40 transition-all duration-200 group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-500">{{ $assignment->course->title }}</span>
                                        </div>
                                        <h3 class="text-white font-medium group-hover:text-red-300 transition-colors">{{ $assignment->title }}</h3>
                                        <p class="text-xs text-red-400 mt-1">{{ __('Due') }} {{ $assignment->due_date ? $assignment->due_date->diffForHumans() : '—' }}</p>
                                    </div>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-red-500/10 text-red-400 shrink-0">{{ __('Overdue') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pending --}}
            @if(count($pending) > 0)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Pending') }}</h2>
                        <span class="text-xs bg-yellow-500/10 text-yellow-400 font-semibold px-2 py-0.5 rounded-md">{{ count($pending) }}</span>
                    </div>
                    <div class="grid gap-3">
                        @foreach($pending as $assignment)
                            <a href="{{ route('courses.assignments.show', [$assignment->course, $assignment]) }}"
                               class="bg-surface-800 border border-white/10 rounded-xl p-4 hover:border-yellow-500/30 transition-all duration-200 group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-500">{{ $assignment->course->title }}</span>
                                        </div>
                                        <h3 class="text-white font-medium group-hover:text-yellow-300 transition-colors">{{ $assignment->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Due') }} {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : '—' }}</p>
                                    </div>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-yellow-500/10 text-yellow-400 shrink-0">{{ __('Pending') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Submitted --}}
            @if(count($submitted) > 0)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Submitted') }}</h2>
                        <span class="text-xs bg-green-500/10 text-green-400 font-semibold px-2 py-0.5 rounded-md">{{ count($submitted) }}</span>
                    </div>
                    <div class="grid gap-3">
                        @foreach($submitted as $assignment)
                            @php $sub = $assignment->submissions->where('user_id', auth()->id())->first(); @endphp
                            <a href="{{ route('courses.assignments.show', [$assignment->course, $assignment]) }}"
                               class="bg-surface-800 border border-white/10 rounded-xl p-4 hover:border-green-500/30 transition-all duration-200 group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-500">{{ $assignment->course->title }}</span>
                                        </div>
                                        <h3 class="text-white font-medium group-hover:text-green-300 transition-colors">{{ $assignment->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Submitted') }} {{ $sub ? $sub->created_at->diffForHumans() : '' }}</p>
                                    </div>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-green-500/10 text-green-400 shrink-0">{{ __('Submitted') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Graded --}}
            @if(count($graded) > 0)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 rounded-full bg-brand-500"></div>
                        <h2 class="text-lg font-semibold text-white">{{ __('Graded') }}</h2>
                        <span class="text-xs bg-brand-500/10 text-brand-400 font-semibold px-2 py-0.5 rounded-md">{{ count($graded) }}</span>
                    </div>
                    <div class="grid gap-3">
                        @foreach($graded as $assignment)
                            @php
                                $sub = $assignment->submissions->where('user_id', auth()->id())->first();
                                $grade = $sub ? $sub->grade : null;
                            @endphp
                            <a href="{{ route('courses.assignments.show', [$assignment->course, $assignment]) }}"
                               class="bg-surface-800 border border-white/10 rounded-xl p-4 hover:border-brand-500/30 transition-all duration-200 group">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-500">{{ $assignment->course->title }}</span>
                                        </div>
                                        <h3 class="text-white font-medium group-hover:text-brand-300 transition-colors">{{ $assignment->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">{{ $grade ? $grade->updated_at->diffForHumans() : '' }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-brand-400 font-bold text-lg">{{ $grade ? number_format($grade->score, 1) : '—' }}</span>
                                        <span class="text-gray-500 text-xs">/{{ $assignment->max_score }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-layouts.dashboard>
