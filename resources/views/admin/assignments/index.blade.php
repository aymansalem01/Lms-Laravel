<x-layouts.dashboard>
    <x-slot name="title">Assignment Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Assignments</h1>
        <button @click="$dispatch('open-create-assignment')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Assignment
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Due Date</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Max Score</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Submissions</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $assignment->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $assignment->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $assignment->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $assignment->max_score ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $assignment->submissions_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.assignments.destroy', $assignment) }}" onsubmit="return confirm('Delete this assignment permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $assignments->links() }}

    {{-- Create Assignment Modal --}}
    <div x-data="{ open: false }" @open-create-assignment.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.assignmentCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Assignment</h3>
            <form method="POST" action="{{ route('admin.assignments.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="assignment_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="assignment_course_id" x-ref="assignmentCourse" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="assignment_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="assignment_title" name="title" type="text" placeholder="Assignment title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="assignment_description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                    <textarea id="assignment_description" name="description" rows="3" placeholder="Assignment description..." class="input-dashboard resize-none"></textarea>
                </div>
                <div class="mb-4">
                    <label for="assignment_due_date" class="block text-sm font-medium text-gray-300 mb-1.5">Due Date</label>
                    <input id="assignment_due_date" name="due_date" type="date" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="assignment_max_score" class="block text-sm font-medium text-gray-300 mb-1.5">Max Score</label>
                    <input id="assignment_max_score" name="max_score" type="number" value="100" min="0" class="input-dashboard">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
