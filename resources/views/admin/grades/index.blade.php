<x-layouts.dashboard>
    <x-slot name="title">Grade Management — Luminus LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Grades</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.grades.import-example') }}" class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-4 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Example CSV
            </a>
            <form method="POST" action="{{ route('admin.grades.import') }}" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                @csrf
                <label class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-4 py-2 text-sm font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import CSV
                    <input type="file" name="csv_file" accept=".csv,.txt" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
            <form method="GET" action="{{ route('admin.grades.export') }}" class="inline-flex items-center gap-2">
                <select name="course_id" class="input-dashboard text-xs py-1.5 px-2 min-w-[160px]">
                    <option value="">All Courses</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Grades</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? $grades->total() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Average</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['avg'] ?? '—' }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Highest</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['highest'] ?? '—' }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Lowest</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['lowest'] ?? '—' }}</p>
        </div>
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
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Score</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Grade</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Date</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($grades as $grade)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.grades.show', $grade) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $grade->submission->student->name ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $grade->submission->assignment->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $grade->submission->assignment->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $grade->score ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $score = (int) ($grade->score ?? 0);
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($score < 50) bg-red-500/20 text-red-400
                                    @elseif($score < 70) bg-amber-500/20 text-amber-400
                                    @elseif($score < 90) bg-emerald-500/20 text-emerald-400
                                    @else bg-brand-500/20 text-brand-300
                                    @endif">
                                    {{ $score }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $grade->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $grade->graded_at ? \Carbon\Carbon::parse($grade->graded_at)->format('M d, Y') : $grade->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.grades.show', $grade) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}" onsubmit="return confirm('Delete this grade? The submission will be reverted to ungraded.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">No grades found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $grades->links() }}
</x-layouts.dashboard>
