<x-layouts.dashboard>
    <x-slot name="title">Grade — {{ $grade->submission->student->name ?? 'Student' }} — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.grades.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to grades
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white">Grade Details</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $grade->submission->assignment->title ?? '—' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.grades.edit', $grade) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}" onsubmit="return confirm('Delete this grade? The submission will be reverted.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                {{-- Score Display --}}
                <div class="flex items-center justify-center mb-6">
                    <div class="w-32 h-32 rounded-full flex items-center justify-center text-3xl font-bold
                        @php $score = (int) ($grade->score ?? 0); @endphp
                        {{ $score < 50 ? 'bg-red-500/20 text-red-400' : ($score < 70 ? 'bg-amber-500/20 text-amber-400' : ($score < 90 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-brand-500/20 text-brand-300')) }}">
                        {{ $grade->score ?? '—' }}
                    </div>
                </div>

                {{-- Feedback --}}
                @if($grade->feedback)
                    <div class="bg-surface-700/50 rounded-lg p-4 mb-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-2">Feedback</p>
                        <p class="text-gray-300 text-sm">{{ $grade->feedback }}</p>
                    </div>
                @endif

                {{-- Plagiarism Report --}}
                @if($grade->submission->submissionFingerprint ?? false)
                    <div class="bg-surface-700/50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-2">Plagiarism Report</p>
                        <p class="text-gray-400 text-sm">Fingerprint: {{ $grade->submission->submissionFingerprint }}</p>
                        @if(isset($plagiarismReport) && $plagiarismReport)
                            <a href="{{ $plagiarismReport }}" target="_blank" class="text-brand-400 hover:text-brand-300 text-sm">View Report</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Student</h4>
                <p class="text-white text-sm">{{ $grade->submission->student->name ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $grade->submission->student->email ?? '' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Assignment</h4>
                <p class="text-white text-sm">{{ $grade->submission->assignment->title ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $grade->submission->assignment->course->title ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Graded By</h4>
                <p class="text-white text-sm">{{ $grade->instructor->name ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $grade->graded_at ? \Carbon\Carbon::parse($grade->graded_at)->format('M d, Y H:i') : '' }}</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
