<x-layouts.dashboard>
    <x-slot name="title">Quiz Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Quizzes</h1>
        <button @click="$dispatch('open-create-quiz')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Quiz
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? $quizzes->total() }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Published</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['published'] ?? 0 }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Draft</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $stats['draft'] ?? 0 }}</p>
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
            <option value="">All Statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
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
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Questions</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Attempts</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Status</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($quizzes as $quiz)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $quiz->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $quiz->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $quiz->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $quiz->questions_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $quiz->attempts_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($quiz->is_published)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Published</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.quizzes.toggle-publish', $quiz) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">
                                            {{ $quiz->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz and all its questions?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No quizzes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $quizzes->links() }}

    {{-- Create Quiz Modal --}}
    <div x-data="{ open: false }" @open-create-quiz.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.quizCourse.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Quiz</h3>
            <form method="POST" action="{{ route('admin.quizzes.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="quiz_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="quiz_course_id" x-ref="quizCourse" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quiz_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="quiz_title" name="title" type="text" placeholder="Quiz title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="quiz_description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                    <textarea id="quiz_description" name="description" rows="3" placeholder="Quiz description..." class="input-dashboard resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quiz_max_attempts" class="block text-sm font-medium text-gray-300 mb-1.5">Max Attempts</label>
                        <input id="quiz_max_attempts" name="max_attempts" type="number" value="1" min="0" class="input-dashboard">
                    </div>
                    <div>
                        <label for="quiz_time_limit" class="block text-sm font-medium text-gray-300 mb-1.5">Time Limit (mins)</label>
                        <input id="quiz_time_limit" name="time_limit" type="number" value="30" min="0" class="input-dashboard">
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
