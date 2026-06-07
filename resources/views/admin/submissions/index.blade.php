<x-layouts.dashboard>
    <x-slot name="title">Submission Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Submissions</h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? $submissions->total() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Graded</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['graded'] ?? 0 }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Late</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['late'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        <select class="input-dashboard">
            <option value="">All Courses</option>
            @foreach($courses ?? [] as $course)
                <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </select>
        <select class="input-dashboard">
            <option value="">All Assignments</option>
            @foreach($assignments ?? [] as $assignment)
                <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
            @endforeach
        </select>
        <select class="input-dashboard">
            <option value="">All Statuses</option>
            <option value="submitted">Submitted</option>
            <option value="graded">Graded</option>
            <option value="late">Late</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Student</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Assignment</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Submitted</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Score</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.submissions.show', $submission) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $submission->student->name ?? '—' }}</a>
                                <p class="text-gray-500 text-xs">{{ $submission->student->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $submission->assignment->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $submission->assignment->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($submission->status === 'graded') bg-emerald-500/20 text-emerald-400
                                    @elseif($submission->status === 'late') bg-red-500/20 text-red-400
                                    @else bg-blue-500/20 text-blue-400
                                    @endif">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y') : $submission->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $submission->grade->score ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.submissions.show', $submission) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" onsubmit="return confirm('Delete this submission permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No submissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $submissions->links() }}
</x-layouts.dashboard>
