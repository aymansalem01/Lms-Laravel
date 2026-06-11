<x-layouts.dashboard>
    <x-slot name="title">Enrollment Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Enrollments</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.enrollments.export-example') }}" class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Example CSV
            </a>
            <a href="{{ route('admin.enrollments.export') }}?{{ request()->getQueryString() }}" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <button @click="$dispatch('open-bulk-enroll')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Bulk Enroll
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Enrollments</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
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
            <option value="">All Programs</option>
            @foreach($programs ?? [] as $program)
                <option value="{{ $program }}">{{ $program }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Student</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Enrolled Date</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="min-w-0">
                                    <p class="text-white font-medium">{{ $enrollment->student->name ?? '—' }}</p>
                                    <p class="text-gray-500 text-xs">{{ $enrollment->student->email ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $enrollment->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $enrollment->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $enrollment->enrolled_at ? \Carbon\Carbon::parse($enrollment->enrolled_at)->format('M d, Y') : $enrollment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" onsubmit="return confirm('Delete this enrollment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $enrollments->links() }}

    {{-- Bulk Enroll Modal --}}
    <div x-data="{ open: false }" @open-bulk-enroll.window="open = true" x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Enroll by CSV</h3>
            <form method="POST" action="{{ route('admin.enrollments.bulk') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Courses to enroll into</label>
                    <div class="max-h-40 overflow-y-auto space-y-1.5 bg-surface-700 rounded-xl p-3 border border-white/10">
                        @foreach($courses ?? [] as $course)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                       class="w-4 h-4 rounded border-white/20 bg-surface-800 text-brand-500 focus:ring-brand-500">
                                <span class="text-sm text-gray-300">{{ $course->title }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">CSV with student emails</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="input-dashboard w-full text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">CSV must have an <strong>email</strong> column with one student email per row.</p>
                </div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.enrollments.bulk-example') }}" class="text-xs text-brand-400 hover:text-brand-300">Download example CSV</a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Enroll</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
