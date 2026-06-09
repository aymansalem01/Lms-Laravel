<x-layouts.dashboard>
    <x-slot name="title">Enrollment Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Enrollments</h1>
        <button @click="$dispatch('open-bulk-enroll')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Bulk Enroll
        </button>
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
                <option value="{{ $program['id'] ?? $program->id }}">{{ $program['name'] ?? $program->name }}</option>
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
         x-effect="open && $nextTick(() => $refs.bulkCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Enroll</h3>
            <form method="POST" action="{{ route('admin.enrollments.bulk') }}">
                @csrf
                <div class="mb-4">
                    <label for="bulk_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="bulk_course_id" x-ref="bulkCourse" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="bulk_student_ids" class="block text-sm font-medium text-gray-300 mb-1.5">Students</label>
                    <select id="bulk_student_ids" name="student_ids[]" multiple class="input-dashboard h-32">
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple students.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Enroll Selected</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
