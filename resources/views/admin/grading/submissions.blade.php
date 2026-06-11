<x-layouts.dashboard>
    <x-slot name="title">{{ $assignment->title }} — {{ $course->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.grading.assignments', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to assignments
        </a>
        <div class="flex items-center justify-between mt-1">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $assignment->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $course->title }} &middot; Max score: {{ $assignment->max_score ?? 100 }}</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Graded</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $stats['graded'] }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Late</p>
            <p class="text-2xl font-bold text-red-400 mt-1">{{ $stats['late'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        <select name="status" class="input-dashboard" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
            <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
        </select>
        <input type="text" name="search" placeholder="Search student name or email..." value="{{ request('search') }}"
               class="input-dashboard flex-1">
        <button type="submit" class="text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors border border-white/10">Filter</button>
        @if(request()->anyFilled(['status', 'search']))
            <a href="{{ route('admin.grading.submissions', [$course, $assignment]) }}" class="text-xs text-gray-500 hover:text-white transition-colors">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Student</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Submitted</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Score</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Grader</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium">{{ $submission->student->name ?? '—' }}</p>
                                <p class="text-gray-500 text-xs">{{ $submission->student->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($submission->status === 'graded') bg-emerald-500/20 text-emerald-400
                                    @elseif($submission->status === 'late') bg-red-500/20 text-red-400
                                    @else bg-blue-500/20 text-blue-400
                                    @endif">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($submission->grade)
                                    <span class="font-semibold {{ (int)$submission->grade->score < 50 ? 'text-red-400' : ((int)$submission->grade->score < 70 ? 'text-amber-400' : 'text-emerald-400') }}">
                                        {{ $submission->grade->score }}
                                    </span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-sm">{{ $submission->grade->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.submissions.show', $submission) }}"
                                   class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-600 transition-colors inline-flex items-center gap-1">
                                    Grade
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $submissions->links() }}
    </div>
</x-layouts.dashboard>
