<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — Admin</x-slot>

    @php $activeTab = request('tab', 'overview'); @endphp

    {{-- Back link --}}
    <div class="mb-6">
        <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Courses') }}</a>
    </div>

    {{-- Course header --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    @if($course->program)
                        <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-brand-500/10 text-brand-400">{{ $course->program }}</span>
                    @endif
                    @if($course->course_type)
                        <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-surface-600 text-gray-400">{{ $course->course_type }}</span>
                    @endif
                    @if($course->is_published)
                        <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400">Published</span>
                    @else
                        <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-400">Draft</span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                <p class="text-sm text-gray-400 mt-1">
                    Instructor: <span class="text-gray-300">{{ $course->instructor->name ?? '—' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.courses.edit', $course) }}" class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Course
                </a>
                <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-sm px-3 py-2 rounded-lg font-medium transition-colors {{ $course->is_published ? 'bg-amber-500/20 text-amber-400 hover:bg-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' }}">
                        {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('courses.content.index', $course) }}" class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Manage Content
                </a>
                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course permanently? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="flex items-center gap-1.5 text-sm bg-red-500/20 text-red-400 hover:bg-red-500/30 px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        @if($course->description)
            <p class="text-sm text-gray-500 mb-4">{{ $course->description }}</p>
        @endif

        <div class="flex flex-wrap gap-x-10 gap-y-2 pt-4 border-t border-white/10">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-white">{{ $course->modules_count ?? $course->modules->count() }}</span>
                <span class="text-xs text-gray-500">Modules</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-white">{{ $course->enrollments_count ?? $course->enrollments->count() }}</span>
                <span class="text-xs text-gray-500">Enrolled</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-white">{{ $course->assignments_count ?? $assignments->count() }}</span>
                <span class="text-xs text-gray-500">Assignments</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-white">{{ $course->quizzes_count ?? $quizzes->count() }}</span>
                <span class="text-xs text-gray-500">Quizzes</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-white">{{ $totalSubmissions ?? 0 }}</span>
                <span class="text-xs text-gray-500">Submissions</span>
            </div>
        </div>

        @if($avgScore)
            <div class="mt-4 pt-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Average Score</span>
                    <div class="flex-1 max-w-xs h-1.5 bg-surface-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-500 to-emerald-400 rounded-full" style="width: {{ min($avgScore, 100) }}%"></div>
                    </div>
                    <span class="text-xs font-medium text-white">{{ number_format($avgScore, 1) }}%</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Reassign Instructor --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl p-5 mb-6">
        <form method="POST" action="{{ route('admin.courses.reassign', $course) }}" class="flex items-end gap-4">
            @csrf @method('PUT')
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Reassign Instructor</label>
                <select name="instructor_id" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ $course->instructor_id === $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-medium rounded-lg px-5 py-2.5 transition-colors">Update</button>
        </form>
    </div>

    {{-- Tab navigation --}}
    <div class="flex items-center gap-1 mb-6 overflow-x-auto scrollbar-thin border-b border-white/10 pb-px">
        @php
            $tabs = [
                'overview'     => ['label' => 'Overview',     'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                'content'      => ['label' => 'Content',      'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                'assignments'  => ['label' => 'Assignments',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                'quizzes'      => ['label' => 'Quizzes',      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'live'         => ['label' => 'Live',         'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                'discussions'  => ['label' => 'Discussions',  'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                'attendance'   => ['label' => 'Attendance',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'students'     => ['label' => 'Students',     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
                'grades'       => ['label' => 'Grades',       'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                'question-bank' => ['label' => 'Question Bank', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                'rubrics'      => ['label' => 'Rubrics',      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($tabs as $tab => $info)
            <a href="{{ route('admin.courses.show', ['course' => $course, 'tab' => $tab]) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors -mb-px
                      {{ $activeTab === $tab ? 'border-brand-500 text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/></svg>
                {{ $info['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('overview')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Enrolled Students ({{ $course->enrollments->count() }})</h3>
                    <div class="space-y-2">
                        @forelse($course->enrollments as $enrollment)
                            <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-surface-700/50">
                                <div class="w-7 h-7 rounded-full gb flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($enrollment->student->name ?? '?', 0, 1)) }}</div>
                                <span class="text-sm text-gray-300">{{ $enrollment->student->name ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-500 ml-auto">{{ $enrollment->student->email ?? '' }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No enrolled students</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.courses.show', ['course' => $course, 'tab' => 'students']) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">Manage Students &rarr;</a>
                    </div>
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Recent Submissions</h3>
                    <div class="space-y-2">
                        @forelse($course->assignments->flatMap->submissions->sortByDesc('created_at')->take(10) as $submission)
                            <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-surface-700/50">
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-300 truncate">{{ $submission->student->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $submission->assignment->title ?? '—' }}</p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $submission->status === 'graded' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                    {{ $submission->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No submissions yet</p>
                        @endforelse
                    </div>
                    @if($assignments->isNotEmpty())
                        <div class="mt-4">
                            <a href="{{ route('grading.index') }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">Go to Grading &rarr;</a>
                        </div>
                    @endif
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Quick Stats</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-surface-700/50 rounded-lg p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $course->modules_count ?? $modules->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Modules</p>
                        </div>
                        <div class="bg-surface-700/50 rounded-lg p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $course->lessons_count ?? $modules->flatMap->lessons->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Lessons</p>
                        </div>
                        <div class="bg-surface-700/50 rounded-lg p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $course->assignments_count ?? $assignments->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Assignments</p>
                        </div>
                        <div class="bg-surface-700/50 rounded-lg p-4 text-center">
                            <p class="text-lg font-bold text-white">{{ $course->quizzes_count ?? $quizzes->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Quizzes</p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Grading Overview</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Total Submissions</span>
                            <span class="text-sm font-medium text-white">{{ $totalSubmissions }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Graded</span>
                            <span class="text-sm font-medium text-emerald-400">{{ $gradedSubmissions }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Pending</span>
                            <span class="text-sm font-medium text-amber-400">{{ $totalSubmissions - $gradedSubmissions }}</span>
                        </div>
                        @if($avgScore)
                            <div class="flex items-center justify-between pt-2 border-t border-white/10">
                                <span class="text-sm text-gray-400">Average Score</span>
                                <span class="text-sm font-bold text-white">{{ number_format($avgScore, 1) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @break

        @case('content')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.content.index', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Manage Content
                    </a>
                </div>

                @forelse($modules as $module)
                    <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left transition-colors hover:bg-surface-700/50">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white">{{ $module->title }}</p>
                                    @if($module->description)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $module->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('courses.content.edit', [$course, $module]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                <span class="text-xs text-gray-500">{{ $module->lessons->count() }} {{ __('lessons') }}{{ $module->quizzes->count() ? ', ' . $module->quizzes->count() . ' quizzes' : '' }}{{ $module->assignments->count() ? ', ' . $module->assignments->count() . ' assignments' : '' }}</span>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="border-t border-white/10 px-5 py-3 space-y-1">
                                @forelse($module->lessons as $lesson)
                                    <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                                        <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}" class="flex items-center gap-3 text-sm text-gray-300 hover:text-white">
                                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ $lesson->title }}</span>
                                        </a>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('courses.content.lesson.edit', [$course, $lesson]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                            <form method="POST" action="{{ route('courses.content.lesson.destroy', [$course, $lesson]) }}" onsubmit="return confirm('Delete this lesson?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 px-3 py-2">No lessons in this module.</p>
                                @endforelse

                                <div class="pt-2">
                                    <a href="{{ route('courses.content.lesson.create', $course) }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">+ Add Lesson</a>
                                </div>

                                {{-- Module resources --}}
                                @php
                                    $modQuizzes = $module->quizzes ?? collect();
                                    $modAssignments = $module->assignments ?? collect();
                                    $modSessions = $module->liveSessions ?? collect();
                                @endphp

                                @if($modQuizzes->isNotEmpty() || $modAssignments->isNotEmpty() || $modSessions->isNotEmpty())
                                    <div class="border-t border-white/10 pt-2 mt-2 space-y-0.5">
                                        @foreach($modQuizzes as $resource)
                                            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                                                <a href="{{ route('courses.quizzes.show', [$course, $resource]) }}" class="flex items-center gap-3 text-sm text-gray-400 hover:text-brand-400 min-w-0 flex-1">
                                                    <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="truncate">{{ $resource->title }}</span>
                                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-brand-500/10 text-brand-400 shrink-0">{{ __('Quiz') }}</span>
                                                </a>
                                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-2">
                                                    <a href="{{ route('courses.quizzes.edit', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                                    <form method="POST" action="{{ route('courses.quizzes.destroy', [$course, $resource]) }}" onsubmit="return confirm('Delete this quiz?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach

                                        @foreach($modAssignments as $resource)
                                            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                                                <a href="{{ route('courses.assignments.show', [$course, $resource]) }}" class="flex items-center gap-3 text-sm text-gray-400 hover:text-blue-400 min-w-0 flex-1">
                                                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span class="truncate">{{ $resource->title }}</span>
                                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 shrink-0">{{ __('Assignment') }}</span>
                                                </a>
                                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-2">
                                                    <a href="{{ route('courses.assignments.edit', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                                    <form method="POST" action="{{ route('courses.assignments.destroy', [$course, $resource]) }}" onsubmit="return confirm('Delete this assignment?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach

                                        @foreach($modSessions as $resource)
                                            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                                                <a href="{{ route('courses.live.show', [$course, $resource]) }}" class="flex items-center gap-3 text-sm text-gray-400 hover:text-coral-400 min-w-0 flex-1">
                                                    <svg class="w-4 h-4 text-coral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    <span class="truncate">{{ $resource->title }}</span>
                                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-coral-500/10 text-coral-400 shrink-0">{{ __('Live') }}</span>
                                                </a>
                                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-2">
                                                    <a href="{{ route('courses.live.edit', [$course, $resource]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                                    <form method="POST" action="{{ route('courses.live.destroy', [$course, $resource]) }}" onsubmit="return confirm('Delete this session?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-surface-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-gray-400 text-sm mb-1">No content yet.</p>
                        <a href="{{ route('courses.content.index', $course) }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">Add modules and lessons</a>
                    </div>
                @endforelse
            </div>
        @break

        @case('assignments')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.assignments.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Assignment
                    </a>
                    <a href="{{ route('assignments.index') }}"
                       class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        All Assignments
                    </a>
                </div>
                @forelse($assignments as $assignment)
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $assignment->title }}</a>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">Due: {{ $assignment->due_date?->format('M d, Y \a\t h:i A') ?? 'No due date' }}</span>
                                <span class="text-xs text-gray-600">|</span>
                                <span class="text-xs text-gray-500">{{ $assignment->max_score ?? 100 }} points</span>
                                <span class="text-xs text-gray-600">|</span>
                                <span class="text-xs text-gray-500">{{ $assignment->submissions->count() }} submissions</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.assignments.edit', [$course, $assignment]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                            <a href="{{ route('courses.assignments.gradebook', [$course, $assignment]) }}" class="text-xs text-brand-400 hover:text-brand-300 px-2 py-1 rounded hover:bg-surface-600 transition-colors">Grade</a>
                            <form method="POST" action="{{ route('courses.assignments.destroy', [$course, $assignment]) }}" onsubmit="return confirm('Delete this assignment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-gray-500 text-sm">No assignments yet.</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('quizzes')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.quizzes.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Quiz
                    </a>
                </div>
                @forelse($quizzes as $quiz)
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $quiz->title }}</a>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">{{ $quiz->questions_count ?? $quiz->questions->count() ?? 0 }} questions</span>
                                <span class="text-xs text-gray-600">|</span>
                                <span class="text-xs text-gray-500">Pass: {{ $quiz->passing_score ?? 70 }}%</span>
                                @if($quiz->time_limit)
                                    <span class="text-xs text-gray-600">|</span>
                                    <span class="text-xs text-gray-500">{{ $quiz->time_limit }} min</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.quizzes.edit', [$course, $quiz]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                            <a href="{{ route('courses.quizzes.review', [$course, $quiz]) }}" class="text-xs text-brand-400 hover:text-brand-300 px-2 py-1 rounded hover:bg-surface-600 transition-colors">Review</a>
                            <form method="POST" action="{{ route('courses.quizzes.destroy', [$course, $quiz]) }}" onsubmit="return confirm('Delete this quiz?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-500 text-sm">No quizzes yet.</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('live')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.live.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Schedule Session
                    </a>
                </div>
                @forelse($liveSessions as $session)
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $session->title }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">{{ $session->scheduled_at ? $session->scheduled_at->format('M d, Y \a\t h:i A') : 'Not scheduled' }}</span>
                                @if($session->duration)
                                    <span class="text-xs text-gray-600">|</span>
                                    <span class="text-xs text-gray-500">{{ $session->duration }} min</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($session->join_url)
                                <a href="{{ $session->join_url }}" target="_blank" class="flex items-center gap-1.5 text-xs bg-coral-500 hover:bg-coral-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Join
                                </a>
                            @endif
                            <form method="POST" action="{{ route('live.destroy', $session) }}" onsubmit="return confirm('Delete this session?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500 text-sm">No live sessions scheduled.</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('discussions')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.discussions.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Start Discussion
                    </a>
                </div>
                @forelse($discussions as $discussion)
                    <a href="{{ route('courses.discussions.show', [$course, $discussion]) }}" class="block bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/30 transition-all">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white">
                                    @if($discussion->is_pinned)
                                        <svg class="w-3.5 h-3.5 text-brand-400 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                    @endif
                                    @if($discussion->is_locked)
                                        <svg class="w-3.5 h-3.5 text-gray-500 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                                    @endif
                                    {{ $discussion->title }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $discussion->user->name ?? 'Anonymous' }}
                                    &middot;
                                    {{ $discussion->created_at->diffForHumans() }}
                                    &middot;
                                    {{ $discussion->replies_count ?? 0 }} replies
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p class="text-gray-500 text-sm">No discussions yet.</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('attendance')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.attendance.report', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-lg transition-colors">
                        Attendance Report
                    </a>
                    <a href="{{ route('courses.attendance.index', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Manage Attendance
                    </a>
                </div>

                @if($attendance->isNotEmpty())
                    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-white/10 bg-surface-700/50">
                                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Student</th>
                                        <th class="text-left px-4 py-3 text-gray-400 font-medium">Date</th>
                                        <th class="text-center px-4 py-3 text-gray-400 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($attendance->take(20) as $record)
                                        <tr class="hover:bg-surface-700/30 transition-colors">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-surface-600 flex items-center justify-center text-xs font-bold text-gray-300">
                                                        {{ strtoupper(substr($record->student->name ?? '?', 0, 1)) }}
                                                    </div>
                                                    <span class="text-sm font-medium text-white">{{ $record->student->name ?? 'Unknown' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-300">{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('M d, Y') : '—' }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                    {{ $record->status === 'present' ? 'bg-green-500/10 text-green-400' : '' }}
                                                    {{ $record->status === 'absent' ? 'bg-red-500/10 text-red-400' : '' }}
                                                    {{ $record->status === 'late' ? 'bg-yellow-500/10 text-yellow-400' : '' }}
                                                    {{ $record->status === 'excused' ? 'bg-blue-500/10 text-blue-400' : '' }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-gray-500 text-sm">No attendance records yet.</p>
                    </div>
                @endif
            </div>
        @break

        @case('students')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.roster', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                        Manage Students
                    </a>
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white">Enrolled Students</h3>
                        <span class="text-xs text-gray-500">{{ count($course->students ?? []) }} enrolled</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Program</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse(($course->students ?? []) as $student)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-medium text-white">{{ $student->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-gray-400">{{ $student->email ?? '—' }}</td>
                                        <td class="px-5 py-3.5">
                                            @if($student->program ?? false)
                                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400">{{ $student->program }}</span>
                                            @else
                                                <span class="text-sm text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <form method="POST" action="{{ route('courses.roster.remove', [$course, $student]) }}" onsubmit="return confirm('Remove {{ $student->name }} from this course?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                                            </div>
                                            <p class="text-gray-400 text-sm">No students enrolled yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @php $studentOptions = $availableStudents->map(fn($s) => ['id' => (string)$s->id, 'label' => $s->name . ' (' . $s->email . ')'])->values(); @endphp
                <div x-data="{
                    search: '',
                    open: false,
                    selected: [],
                    items: {{ json_encode($studentOptions) }},
                    get filtered() {
                        return this.items.filter(s => s.label.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }" class="bg-surface-800 border border-white/10 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-white mb-3">Enroll Students</h3>
                    <form method="POST" action="{{ route('courses.roster.add', $course) }}">
                        @csrf
                        <div class="relative mb-3">
                            <input type="text" x-model="search" @click="open = true" @input="open = true"
                                   placeholder="Search students..."
                                   class="w-full bg-surface-700 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-10 mt-1 w-full bg-surface-700 border border-white/10 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                                <template x-for="student in filtered" :key="student.id">
                                    <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-600 cursor-pointer border-b border-white/10 last:border-0">
                                        <input type="checkbox" x-model="selected" :value="student.id"
                                               class="rounded bg-surface-600 border-surface-500 text-brand-500 focus:ring-brand-500/50">
                                        <span class="text-sm text-gray-200" x-text="student.label"></span>
                                    </label>
                                </template>
                                <p x-show="filtered.length === 0" class="text-sm text-gray-500 px-3 py-4 text-center">No students found.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-3" x-show="selected.length > 0">
                            <template x-for="id in selected" :key="id">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-400 text-xs">
                                    <span x-text="items.find(s => s.id === id)?.label.split(' (')[0]"></span>
                                    <button @click="selected = selected.filter(s => s !== id)" type="button" class="hover:text-white">&times;</button>
                                </span>
                            </template>
                        </div>
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="student_ids[]" :value="id">
                        </template>
                        <div class="flex items-center gap-2">
                            <button type="submit" :disabled="selected.length === 0"
                                    class="text-sm bg-brand-500 hover:bg-brand-600 disabled:bg-surface-600 disabled:text-gray-500 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                                <span x-text="`Enroll (${selected.length})`"></span>
                            </button>
                            <button type="button" @click="selected = []; search = ''" x-show="selected.length > 0" class="text-xs text-gray-400 hover:text-white transition-colors">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        @break

        @case('grades')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.grade-rules.index', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Manage Grade Rules
                    </a>
                    <a href="{{ route('grading.index') }}"
                       class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Grading Dashboard
                    </a>
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-white mb-3">Grade Rules</h3>
                    @php $totalWeight = $course->gradeRules->sum('weight'); @endphp
                    @forelse($course->gradeRules ?? collect() as $rule)
                        <div class="flex items-center justify-between py-2 border-b border-white/10 last:border-0">
                            <span class="text-sm text-gray-300">{{ ucfirst($rule->category) }}</span>
                            <span class="text-sm font-medium text-white">{{ $rule->weight }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No grade rules configured yet.</p>
                    @endforelse
                    @if($course->gradeRules->isNotEmpty())
                        <div class="flex items-center justify-between py-2 mt-2">
                            <span class="text-sm font-semibold text-white">Total</span>
                            <span class="text-sm font-semibold {{ $totalWeight == 100 ? 'text-emerald-400' : 'text-red-400' }}">{{ $totalWeight }}%</span>
                        </div>
                    @endif
                </div>
            </div>
        @break

        @case('question-bank')
            <div class="space-y-3">
                <div class="flex justify-end mb-3 gap-2">
                    <a href="{{ route('courses.question-bank.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Question
                    </a>
                    <a href="{{ route('question-bank.index') }}"
                       class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Global Question Banks
                    </a>
                </div>

                @forelse($questionBanks as $bank)
                    <div x-data="{ open: false }" class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left transition-colors hover:bg-surface-700/50">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $bank->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $bank->items->count() }} questions</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="border-t border-white/10 px-5 py-3 space-y-1">
                                @forelse($bank->items as $item)
                                    <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-700/50 transition-colors group">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-300 truncate">{{ Str::limit($item->question, 80) }}</p>
                                            <span class="text-xs text-gray-600">{{ ucfirst($item->type) }} &middot; {{ $item->points ?? 1 }} pts</span>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('courses.question-bank.edit', [$course, $item]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                            <form method="POST" action="{{ route('courses.question-bank.destroy', [$course, $item]) }}" onsubmit="return confirm('Delete this question?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600 px-3 py-2">No questions in this bank.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <p class="text-gray-500 text-sm">No question banks attached to this course.</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('rubrics')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.rubrics.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Rubric
                    </a>
                </div>
                @forelse($rubrics as $rubric)
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $rubric->name }}</p>
                            @if($rubric->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $rubric->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.rubrics.edit', [$course, $rubric]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                            <form method="POST" action="{{ route('courses.rubrics.destroy', [$course, $rubric]) }}" onsubmit="return confirm('Delete this rubric?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-500 text-sm">No rubrics yet.</p>
                    </div>
                @endforelse
            </div>
        @break
    @endswitch
</x-layouts.dashboard>