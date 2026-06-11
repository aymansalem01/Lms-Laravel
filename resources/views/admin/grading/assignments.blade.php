<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — Grading — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.grading.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to courses
        </a>
        <h1 class="text-2xl font-bold text-white mt-1">{{ $course->title }}</h1>
        <p class="text-sm text-gray-500 mt-1">Select an assignment to view and grade submissions</p>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Assignment</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Submissions</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Graded</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Pending</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-white font-medium">{{ $assignment->title }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $assignment->submissions_count }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-emerald-400">{{ $assignment->graded_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php $pending = $assignment->submissions_count - $assignment->graded_count; @endphp
                                <span class="{{ $pending > 0 ? 'text-amber-400' : 'text-gray-500' }}">{{ $pending }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.grading.submissions', [$course, $assignment]) }}"
                                   class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-600 transition-colors">
                                    View
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No assignments found for this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $assignments->links() }}
    </div>
</x-layouts.dashboard>
