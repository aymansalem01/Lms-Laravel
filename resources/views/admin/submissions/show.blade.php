<x-layouts.dashboard>
    <x-slot name="title">Submission — {{ $submission->student->name ?? 'Student' }} — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.submissions.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to submissions
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">Submission Details</h2>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this submission?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($submission->status === 'graded') bg-emerald-500/20 text-emerald-400
                        @elseif($submission->status === 'late') bg-red-500/20 text-red-400
                        @else bg-blue-500/20 text-blue-400
                        @endif">
                        {{ ucfirst($submission->status) }}
                    </span>
                </div>

                @if($submission->file_url)
                    <div class="bg-surface-700/50 rounded-lg p-4 mb-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Submitted File</p>
                        <a href="{{ $submission->file_url }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm break-all">{{ $submission->file_url }}</a>
                    </div>
                @endif

                @if($submission->submitted_at)
                    <div class="bg-surface-700/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Submitted At</p>
                        <p class="text-white text-sm">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y H:i') }}</p>
                    </div>
                @endif
            </div>

            {{-- Plagiarism Report --}}
            @if($submission->plagiarismReport ?? false)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Plagiarism Report</h3>
                    <p class="text-gray-400 text-sm">{{ $submission->plagiarismReport }}</p>
                </div>
            @endif

            {{-- Grade Info --}}
            @if($submission->grade)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Grade</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold
                            @php $score = (int) ($submission->grade->score ?? 0); @endphp
                            {{ $score < 50 ? 'bg-red-500/20 text-red-400' : ($score < 70 ? 'bg-amber-500/20 text-amber-400' : ($score < 90 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-brand-500/20 text-brand-300')) }}">
                            {{ $submission->grade->score ?? '—' }}
                        </div>
                        <div>
                            <p class="text-white font-medium">Graded by {{ $submission->grade->instructor->name ?? 'Unknown' }}</p>
                            <p class="text-gray-500 text-xs">{{ $submission->grade->graded_at ? \Carbon\Carbon::parse($submission->grade->graded_at)->format('M d, Y H:i') : '' }}</p>
                            @if($submission->grade->feedback)
                                <p class="text-gray-400 text-sm mt-2">{{ $submission->grade->feedback }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Student</h4>
                <p class="text-white text-sm">{{ $submission->student->name ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $submission->student->email ?? '' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Assignment</h4>
                <p class="text-white text-sm">{{ $submission->assignment->title ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $submission->assignment->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $submission->assignment->course->instructor->name ?? '—' }}</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
