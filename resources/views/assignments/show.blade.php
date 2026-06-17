<x-layouts.dashboard>
    <x-slot name="title">{{ $assignment->title }} — {{ $course->name }}</x-slot>

    @php
        $isInstructor = auth()->user()->role === 'instructor';
        $submissions = $assignment->submissions->where('student_id', auth()->id())->sortByDesc('created_at');
        $submission = $submissions->first();
        $isOverdue = $assignment->due_date && now()->gt($assignment->due_date);
    @endphp

    <div class="mb-6">
        <a href="{{ route('courses.assignments.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to assignments') }}
        </a>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $assignment->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $course->name }} — {{ $course->code ?? '' }}</p>
            </div>
            @if($isInstructor)
                <div class="flex items-center gap-2">
                    <a href="{{ route('courses.assignments.gradebook', [$course, $assignment]) }}"
                       class="bg-surface-700 hover:bg-surface-600 text-white font-medium rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ __('Gradebook') }}
                    </a>
                    <a href="{{ route('courses.assignments.edit', [$course, $assignment]) }}"
                       class="bg-surface-700 hover:bg-surface-600 text-white font-medium rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ __('Edit') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Description') }}</h2>
                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $assignment->description }}</div>

                @if($assignment->rubric_id)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <a href="{{ route('rubrics.show', $assignment->rubric_id) }}" class="inline-flex items-center gap-2 text-sm text-brand-400 hover:text-brand-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('View Grading Rubric') }}
                        </a>
                    </div>
                @endif
            </div>

            @if(!$isInstructor)
                @if(!$submission && !$isOverdue)
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">{{ __('Submit Your Work') }}</h2>
                        <form method="POST" action="{{ route('courses.assignments.submit', [$course, $assignment]) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Upload File') }}</label>
                                <input type="file" name="file" accept=".pdf,.doc,.docx,.zip,.png,.jpg"
                                       class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 file:cursor-pointer file:transition-colors bg-surface-800 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Or Link') }}</label>
                                <input type="url" name="link" placeholder="https://..."
                                       class="input-dashboard">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Notes') }}</label>
                                <textarea name="notes" rows="3" placeholder="{{ __('Add any notes about your submission...') }}"
                                          class="input-dashboard resize-none"></textarea>
                            </div>
                            <button type="submit"
                                    class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                                {{ __('Submit Assignment') }}
                            </button>
                        </form>
                    </div>
                @elseif(!$submission && $isOverdue)
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">{{ __('Submit Your Work') }}</h2>
                        <div class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-6 text-center">
                            <p class="text-sm text-red-400 font-medium">{{ __('This assignment is past due. Submissions are closed.') }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <h2 class="text-lg font-semibold text-white mb-4">{{ __('Your Submission') }}</h2>
                        <div class="space-y-3">
                            @if($submission->file_path)
                                <a href="{{ Storage::url($submission->file_path) }}" target="_blank"
                                   class="flex items-center gap-3 bg-surface-700 rounded-xl px-4 py-3 hover:bg-surface-600 transition-colors group">
                                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">{{ basename($submission->file_path) }}</span>
                                </a>
                            @endif
                            @if($submission->link)
                                <a href="{{ $submission->link }}" target="_blank"
                                   class="flex items-center gap-3 bg-surface-700 rounded-xl px-4 py-3 hover:bg-surface-600 transition-colors group">
                                    <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors truncate">{{ $submission->link }}</span>
                                </a>
                            @endif
                            @if($submission->notes)
                                <div class="bg-surface-700 rounded-xl px-4 py-3">
                                    <p class="text-xs text-gray-500 mb-1">{{ __('Notes') }}</p>
                                    <p class="text-sm text-gray-300">{{ $submission->notes }}</p>
                                </div>
                            @endif
                            <div class="flex items-center gap-2 pt-2">
                                <span class="text-xs text-gray-500">{{ __('Submitted') }} {{ $submission->created_at->diffForHumans() }}</span>
                                @php
                                    $status = match(true) {
                                        $submission->grade && $submission->grade->is_published => 'graded',
                                        $submission->grade => 'pending_review',
                                        default => 'submitted'
                                    };
                                @endphp
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md
                                    {{ $status === 'graded' ? 'bg-green-500/10 text-green-400' : ($status === 'pending_review' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-blue-500/10 text-blue-400') }}">
                                    {{ __(ucwords(str_replace('_', ' ', $status))) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($submission->grade && $submission->grade->is_published)
                        <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">{{ __('Grade & Feedback') }}</h2>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-20 h-20 rounded-2xl gb flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white">{{ number_format($submission->grade->score, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">{{ __('out of') }} {{ $assignment->max_score }}</p>
                                    <p class="text-sm text-gray-400">{{ number_format(($submission->grade->score / $assignment->max_score) * 100, 0) }}%</p>
                                </div>
                            </div>
                            @if($submission->grade->feedback)
                                <div class="bg-surface-700 rounded-xl px-4 py-3">
                                    <p class="text-xs text-gray-500 mb-1">{{ __('Instructor Feedback') }}</p>
                                    <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $submission->grade->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($submissions->count() > 1)
                        <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">{{ __('Past Submissions') }}</h2>
                            <div class="space-y-2">
                                @foreach($submissions->skip(1) as $prev)
                                    <div class="flex items-center justify-between bg-surface-700 rounded-xl px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if($prev->file_path)
                                                <a href="{{ Storage::url($prev->file_path) }}" target="_blank" class="flex items-center gap-2 text-sm text-brand-400 hover:text-brand-300 transition-colors truncate">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="truncate">{{ basename($prev->file_path) }}</span>
                                                </a>
                                            @else
                                                <span class="text-sm text-gray-400">{{ __('No file') }}</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 shrink-0 ml-3">{{ $prev->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>

            <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white mb-3">{{ __('Details') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Due Date') }}</span>
                        <span class="text-gray-300">{{ $assignment->due_date ? $assignment->due_date->format('M d, Y h:i A') : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Max Score') }}</span>
                        <span class="text-gray-300">{{ $assignment->max_score }} {{ __('pts') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Submissions') }}</span>
                        <span class="text-gray-300">{{ $assignment->submissions->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Status') }}</span>
                        <span class="{{ $isOverdue ? 'text-red-400' : 'text-green-400' }}">{{ $isOverdue ? __('Overdue') : __('Open') }}</span>
                    </div>
                    @if($assignment->file_path)
                        <div class="pt-3 border-t border-white/10">
                            <span class="text-gray-500 text-xs block mb-2">{{ __('Attachment') }}</span>
                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank"
                               class="flex items-center gap-2 bg-surface-700 rounded-xl px-3 py-2.5 hover:bg-surface-600 transition-colors group">
                                <svg class="w-5 h-5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-sm text-gray-300 group-hover:text-white transition-colors truncate">{{ basename($assignment->file_path) }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($assignment->rubric)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-white mb-3">{{ __('Rubric') }}</h3>
                    <p class="text-xs text-gray-400">{{ $assignment->rubric->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ count($assignment->rubric->criteria ?? []) }} {{ __('criteria') }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($isInstructor)
        <div class="mt-6">
            <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">{{ __('Submissions') }} ({{ $assignment->submissions->count() }})</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">{{ __('Graded') }}: {{ $assignment->submissions->filter(fn($s) => $s->grade && $s->grade->is_published)->count() }}</span>
                        <span class="text-xs text-gray-500">|</span>
                        <span class="text-xs text-gray-500">{{ __('Pending') }}: {{ $assignment->submissions->filter(fn($s) => !$s->grade || !$s->grade->is_published)->count() }}</span>
                    </div>
                </div>
                @if($assignment->submissions->count() === 0)
                    <div class="p-12 text-center">
                        <svg class="w-10 h-10 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-gray-400">{{ __('No submissions yet') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3">{{ __('Student') }}</th>
                                    <th class="px-6 py-3">{{ __('Status') }}</th>
                                    <th class="px-6 py-3">{{ __('Submitted') }}</th>
                                    <th class="px-6 py-3">{{ __('Grade') }}</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($assignment->submissions->sortByDesc('created_at') as $sub)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($sub->student->name, 0, 1)) }}</div>
                                                <span class="text-gray-300 font-medium">{{ $sub->student->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $s = match(true) {
                                                    $sub->grade && $sub->grade->is_published => ['graded', 'green'],
                                                    $sub->grade => ['pending_review', 'yellow'],
                                                    default => ['submitted', 'blue']
                                                };
                                            @endphp
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-{{ $s[1] }}-500/10 text-{{ $s[1] }}-400">
                                                {{ __(ucwords(str_replace('_', ' ', $s[0]))) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-400">{{ $sub->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4">
                                            @if($sub->grade && $sub->grade->is_published)
                                                <span class="text-green-400 font-medium">{{ number_format($sub->grade->score, 1) }}/{{ $assignment->max_score }}</span>
                                            @elseif($sub->grade)
                                                <span class="text-yellow-400">{{ __('Draft') }}: {{ number_format($sub->grade->score, 1) }}</span>
                                            @else
                                                <span class="text-gray-500">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('grading.show', $sub) }}"
                                               class="inline-flex items-center gap-1.5 text-sm text-brand-400 hover:text-brand-300 transition-colors">
                                                {{ __('Grade') }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-layouts.dashboard>
