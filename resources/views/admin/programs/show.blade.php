<x-layouts.dashboard>
    <x-slot name="title">{{ $program->name }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.programs.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to programs
        </a>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $program->name }}</h1>
            @if($program->description)
                <p class="text-gray-400 text-sm mt-1">{{ $program->description }}</p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.programs.edit', $program) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors">Edit Program</a>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white">Students</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Name</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Email</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($students as $student)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $student) }}" class="text-white font-medium hover:text-brand-300">{{ $student->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $student->email }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.users.unenroll', [$student, 'program' => $program->name]) }}" onsubmit="return confirm('Remove this student from the program?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-gray-500">No students in this program.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($students, 'links'))
            <div class="p-4 border-t border-white/10">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- Courses Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Courses</h3>
            <button @click="$dispatch('assign-course', { programId: {{ $program->id }} })" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Assign Course</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Enrollments</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($courses as $course)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.courses.show', $course) }}" class="text-white font-medium hover:text-brand-300">{{ $course->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $course->enrollments_count ?? $course->enrollments()->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.programs.courses.unassign', [$program, $course]) }}" onsubmit="return confirm('Unassign this course from the program?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Unassign</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">No courses in this program.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assign Course Modal --}}
    <div x-data="{ open: false, programId: null }" @assign-course.window="open = true; programId = $event.detail.programId" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.assignCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Assign Course to Program</h3>
            <form method="POST" action="{{ route('admin.programs.courses.assign', $program) }}">
                @csrf
                <div class="mb-4">
                    <label for="assign_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="assign_course_id" x-ref="assignCourse" name="course_id" class="w-full bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                        <option value="">Select course...</option>
                        @foreach($allCourses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Assign</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
