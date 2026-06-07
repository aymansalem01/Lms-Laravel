<x-layouts.dashboard>
    <x-slot name="title">{{ $assignment->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.assignments.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to assignments
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $assignment->title }}</h2>
                        @if($assignment->description)
                            <p class="text-gray-400 text-sm mt-1">{{ $assignment->description }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.assignments.destroy', $assignment) }}" onsubmit="return confirm('Delete this assignment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Due Date</p>
                        <p class="text-lg font-bold text-white">{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : '—' }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Max Score</p>
                        <p class="text-lg font-bold text-white">{{ $assignment->max_score ?? '—' }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Submissions</p>
                        <p class="text-lg font-bold text-white">{{ $submissionCount ?? $assignment->submissions->count() }}</p>
                    </div>
                    <div class="bg-surface-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Graded</p>
                        <p class="text-lg font-bold text-white">{{ $gradedCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Submissions List --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Submissions ({{ $submissionCount ?? $assignment->submissions->count() }})</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $gradedCount ?? 0 }} graded out of {{ $submissionCount ?? $assignment->submissions->count() }}</p>
                @forelse($assignment->submissions->sortByDesc('created_at') as $submission)
                    <div class="flex items-center justify-between py-2.5 border-b border-white/5 last:border-0">
                        <div>
                            <a href="{{ route('admin.submissions.show', $submission) }}" class="text-sm text-white hover:text-brand-300">{{ $submission->student->name ?? 'Unknown' }}</a>
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium
                                @if($submission->status === 'graded') bg-emerald-500/20 text-emerald-400
                                @elseif($submission->status === 'late') bg-red-500/20 text-red-400
                                @else bg-blue-500/20 text-blue-400
                                @endif">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </div>
                        <div class="text-right">
                            @if($submission->grade)
                                <span class="text-sm font-bold
                                    @php $s = (int) ($submission->grade->score ?? 0); @endphp
                                    {{ $s < 50 ? 'text-red-400' : ($s < 70 ? 'text-amber-400' : ($s < 90 ? 'text-emerald-400' : 'text-brand-300')) }}">
                                    {{ $submission->grade->score }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">—</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No submissions yet.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $assignment->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $assignment->course->instructor->name ?? '—' }}</p>
            </div>
            @if($assignment->rubric)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Rubric</h4>
                    <a href="{{ route('admin.rubrics.show', $assignment->rubric) }}" class="text-brand-400 hover:text-brand-300 text-sm">{{ $assignment->rubric->title }}</a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
