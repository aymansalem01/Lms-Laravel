<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }}</x-slot>

    @php
        $isInstructor = auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id();
        $isAdmin = auth()->user()->role === 'admin';
        $canManage = $isInstructor || $isAdmin;
        $isEnrolled = $isEnrolled ?? false;
        $activeTab = request('tab', 'content');
    @endphp

    {{-- Course header --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-6">
        <div class="md:flex">
            @if($course->cover_image_url)
                <div class="md:w-80 h-48 md:h-auto shrink-0">
                    <img src="{{ $course->cover_image_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="md:w-80 h-48 md:h-auto shrink-0 gb flex items-center justify-center">
                    <span class="text-5xl font-bold text-white/40">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                </div>
            @endif

            <div class="p-6 flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            @if($course->program)
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-brand-500/10 text-brand-400">{{ $course->program }}</span>
                            @endif
                            @if($course->is_published)
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-green-500/10 text-green-400">{{ __('Published') }}</span>
                            @else
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-yellow-500/10 text-yellow-400">{{ __('Draft') }}</span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ __('Instructor') }}:
                            <span class="text-gray-300">{{ $course->instructor->name ?? __('Unknown') }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($canManage)
                            <a href="{{ route('courses.edit', $course) }}" class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-3 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                {{ __('Edit Course') }}
                            </a>
                            <form method="POST" action="{{ route('courses.duplicate', $course) }}" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-gray-300 px-3 py-2 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    {{ __('Duplicate') }}
                                </button>
                            </form>
                            <a href="{{ route('courses.content.index', $course) }}" class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                {{ __('Manage Content') }}
                            </a>
                        @endif
                        @if(auth()->user()->role === 'student' && !$isEnrolled)
                            <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                @csrf
                                <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-5 py-2 rounded-lg transition-colors">
                                    {{ __('Enroll Now') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($course->description)
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $course->description }}</p>
                @endif

                @if($isEnrolled && isset($course->progress))
                    <div class="mt-4 flex items-center gap-3">
                        <span class="text-xs text-gray-500">{{ __('Your progress') }}</span>
                        <div class="flex-1 max-w-xs h-1.5 bg-surface-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-brand-500 to-coral-500 rounded-full" style="width: {{ $course->progress }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-white">{{ $course->progress }}%</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab navigation --}}
    <div class="flex items-center gap-1 mb-6 overflow-x-auto scrollbar-thin border-b border-surface-700 pb-px">
        @php
            $tabs = [
                'content' => ['label' => __('Content'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                'assignments' => ['label' => __('Assignments'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                'quizzes' => ['label' => __('Quizzes'), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'live' => ['label' => __('Live'), 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                'discussions' => ['label' => __('Discussions'), 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                'attendance' => ['label' => __('Attendance'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'students' => ['label' => __('Students'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
                'grades' => ['label' => __('Grades'), 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
            if ($canManage) {
                $tabs['question-bank'] = ['label' => __('Question Bank'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'];
                $tabs['rubrics'] = ['label' => __('Rubrics'), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'];
            }
        @endphp
        @foreach($tabs as $tab => $info)
            <a href="{{ route('courses.show', ['course' => $course, 'tab' => $tab]) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors -mb-px
                      {{ $activeTab === $tab ? 'border-brand-500 text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-300 hover:border-gray-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/></svg>
                {{ $info['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('content')
            <div class="space-y-3">
                @if($canManage)
                    <div class="flex justify-end mb-3">
                        <a href="{{ route('courses.content.index', $course) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            {{ __('Manage Content') }}
                        </a>
                    </div>
                @endif

                @forelse(($modules ?? collect()) as $module)
                    <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left transition-colors hover:bg-surface-700/50">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-white">{{ $module->title }}</p>
                                    @if($module->description)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $module->description }}</p>
                                    @endif
                                    @if(isset($moduleProgress[$module->id]) && $isEnrolled)
                                        @php $mp = $moduleProgress[$module->id]; @endphp
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <div class="flex-1 max-w-[120px] h-1 bg-surface-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-brand-500 to-emerald-400 rounded-full transition-all" style="width: {{ $mp['percent'] }}%"></div>
                                            </div>
                                            <span class="text-[10px] text-gray-500">{{ $mp['completed'] }}/{{ $mp['total'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs text-gray-500">{{ $module->lessons_count ?? $module->lessons->count() ?? 0 }} {{ __('lessons') }}</span>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="border-t border-surface-700 px-5 py-3 space-y-1">
                                @forelse(($module->lessons ?? collect()) as $lesson)
                                    <a href="{{ route('courses.content.lesson.show', [$course, $lesson]) }}"
                                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors {{ $lesson->is_completed ?? false ? 'text-gray-400' : 'text-gray-300 hover:text-white hover:bg-surface-700' }}">
                                        @if($lesson->is_completed ?? false)
                                            <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                        <span>{{ $lesson->title }}</span>
                                    </a>
                                @empty
                                    <p class="text-sm text-gray-600 px-3 py-2">{{ __('No lessons in this module.') }}</p>
                                @endforelse

                                {{-- Module file attachment --}}
                                @if($module->file_path)
                                    <a href="{{ Storage::url($module->file_path) }}" target="_blank"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:text-brand-400 hover:bg-surface-700 transition-colors">
                                        <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="truncate">{{ basename($module->file_path) }}</span>
                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-brand-500/10 text-brand-400 ml-auto">{{ __('Download') }}</span>
                                    </a>
                                @endif

                                {{-- Module-level resources --}}
                                @php
                                    $moduleQuizzes = $module->quizzes ?? collect();
                                    $moduleSessions = $module->liveSessions ?? collect();
                                    $moduleAssignments = $module->assignments ?? collect();
                                    $moduleFiles = $module->moduleFiles ?? collect();
                                @endphp

                                @if($moduleQuizzes->isNotEmpty() || $moduleSessions->isNotEmpty() || $moduleAssignments->isNotEmpty() || $moduleFiles->isNotEmpty())
                                    <div class="border-t border-surface-700 pt-2 mt-2 space-y-0.5">
                                        @foreach($moduleQuizzes as $resource)
                                            <a href="{{ route('courses.quizzes.show', [$course, $resource]) }}"
                                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-brand-400 hover:bg-surface-700 transition-colors">
                                                <svg class="w-4 h-4 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>{{ $resource->title }}</span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-brand-500/10 text-brand-400 ml-auto">{{ __('Quiz') }}</span>
                                            </a>
                                        @endforeach

                                        @foreach($moduleSessions as $resource)
                                            <a href="{{ route('courses.live.show', [$course, $resource]) }}"
                                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-coral-400 hover:bg-surface-700 transition-colors">
                                                <svg class="w-4 h-4 text-coral-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <span>{{ $resource->title }}</span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-coral-500/10 text-coral-400 ml-auto">{{ __('Live') }}</span>
                                            </a>
                                        @endforeach

                                        @foreach($moduleAssignments as $resource)
                                            <a href="{{ route('courses.assignments.show', [$course, $resource]) }}"
                                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-blue-400 hover:bg-surface-700 transition-colors">
                                                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                <span>{{ $resource->title }}</span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 ml-auto">{{ __('Assignment') }}</span>
                                            </a>
                                        @endforeach

                                        @foreach($moduleFiles as $resource)
                                            <a href="{{ Storage::url($resource->file_path) }}" target="_blank"
                                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-amber-400 hover:bg-surface-700 transition-colors">
                                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>{{ $resource->title }}</span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 ml-auto">{{ __('File') }}</span>
                                            </a>
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
                        <p class="text-gray-400 text-sm mb-1">{{ __('No content yet.') }}</p>
                        @if($canManage)
                            <a href="{{ route('courses.content.index', $course) }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">{{ __('Add modules and lessons') }}</a>
                        @endif
                    </div>
                @endforelse
            </div>
        @break

        @case('assignments')
            <div class="space-y-3">
                @if($canManage)
                    <div class="flex justify-end mb-3">
                        <a href="{{ route('courses.assignments.create', $course) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Create Assignment') }}
                        </a>
                    </div>
                @endif
                @forelse(($assignments ?? collect()) as $assignment)
                    <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('courses.assignments.show', [$course, $assignment]) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $assignment->title }}</a>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">{{ __('Due') }}: {{ $assignment->due_date?->format('M d, Y \a\t h:i A') ?? __('No due date') }}</span>
                                <span class="text-xs text-gray-600">|</span>
                                <span class="text-xs text-gray-500">{{ $assignment->max_score ?? 100 }} {{ __('points') }}</span>
                            </div>
                        </div>
                        @auth
                            @if(auth()->user()->role === 'student')
                                @php $hasSubmitted = $assignment->submissions->where('student_id', auth()->id())->isNotEmpty(); @endphp
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full {{ $hasSubmitted ? 'bg-green-500/10 text-green-400' : 'bg-brand-500/10 text-brand-400' }}">
                                    {{ $hasSubmitted ? __('Submitted') : __('Pending') }}
                                </span>
                            @elseif($canManage)
                                <a href="{{ route('courses.assignments.edit', [$course, $assignment]) }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Edit') }}</a>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-gray-500 text-sm">{{ __('No assignments yet.') }}</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('quizzes')
            <div class="space-y-3">
                @forelse(($quizzes ?? collect()) as $quiz)
                    <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $quiz->title }}</a>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">{{ $quiz->questions_count ?? 0 }} {{ __('questions') }}</span>
                                <span class="text-xs text-gray-600">|</span>
                                <span class="text-xs text-gray-500">{{ __('Pass') }}: {{ $quiz->passing_score ?? 70 }}%</span>
                            </div>
                        </div>
                        <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">{{ __('Start') }} &rarr;</a>
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-500 text-sm">{{ __('No quizzes yet.') }}</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('live')
            <div class="space-y-3">
                @forelse(($liveSessions ?? collect()) as $session)
                    <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $session->title }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">{{ $session->scheduled_at ? $session->scheduled_at->format('M d, Y \a\t h:i A') : __('Not scheduled') }}</span>
                                @if($session->duration)
                                    <span class="text-xs text-gray-600">|</span>
                                    <span class="text-xs text-gray-500">{{ $session->duration }} {{ __('min') }}</span>
                                @endif
                            </div>
                        </div>
                        @if($session->join_url)
                            <a href="{{ $session->join_url }}" target="_blank" class="flex items-center gap-1.5 text-sm bg-coral-500 hover:bg-coral-600 text-white px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                {{ __('Join') }}
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500 text-sm">{{ __('No live sessions scheduled.') }}</p>
                    </div>
                @endforelse
            </div>
        @break

        @case('discussions')
            <div class="space-y-3">
                @forelse(($discussions ?? collect()) as $discussion)
                    <a href="{{ route('courses.discussions.show', [$course, $discussion]) }}" class="block bg-surface-800 border border-surface-700 rounded-xl p-5 hover:border-brand-500/30 transition-all">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white flex items-center gap-2">
                                    @if($discussion->is_pinned)
                                        <svg class="w-3.5 h-3.5 text-brand-400" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                                    @endif
                                    @if($discussion->is_locked)
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
                                    @endif
                                    {{ $discussion->title }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $discussion->user->name ?? __('Anonymous') }}
                                    &middot;
                                    {{ $discussion->created_at->diffForHumans() }}
                                    &middot;
                                    {{ $discussion->replies_count ?? 0 }} {{ __('replies') }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                @empty
                    <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                        <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p class="text-gray-500 text-sm">{{ __('No discussions yet.') }}</p>
                    </div>
                @endforelse
                @if($isEnrolled || $canManage)
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('courses.discussions.create', $course) }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-5 py-2.5 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Start a discussion') }}
                        </a>
                    </div>
                @endif
            </div>
        @break

        @case('attendance')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    @if($canManage)
                        <div class="flex gap-2">
                            <a href="{{ route('courses.attendance.report', $course) }}"
                               class="flex items-center gap-1.5 text-sm bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-lg transition-colors">
                                {{ __('Attendance Report') }}
                            </a>
                            <a href="{{ route('courses.attendance.index', $course) }}"
                               class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                                {{ __('Manage Attendance') }}
                            </a>
                        </div>
                    @else
                        <a href="{{ route('courses.attendance.my', $course) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            {{ __('View All My Attendance') }}
                        </a>
                    @endif
                </div>

                @php
                    $displayRecords = $canManage ? ($attendance ?? collect()) : (($attendance ?? collect())->where('student_id', auth()->id()));
                @endphp

                @if($displayRecords->isNotEmpty())
                    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-white/10 bg-surface-700/50">
                                        @if($canManage)<th class="text-left px-4 py-3 text-gray-400 font-medium">{{ __('Student') }}</th>@endif
                                        <th class="text-left px-4 py-3 text-gray-400 font-medium">{{ __('Date') }}</th>
                                        <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($displayRecords->take(20) as $record)
                                        <tr class="hover:bg-surface-700/30 transition-colors">
                                            @if($canManage)
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-surface-600 flex items-center justify-center text-xs font-bold text-gray-300">
                                                            {{ strtoupper(substr($record->student->name ?? '?', 0, 1)) }}
                                                        </div>
                                                        <span class="text-sm font-medium text-white">{{ $record->student->name ?? __('Unknown') }}</span>
                                                    </div>
                                                </td>
                                            @endif
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
                        <p class="text-gray-500 text-sm">{{ __('No attendance records yet.') }}</p>
                    </div>
                @endif
            </div>
        @break

        @case('students')
            <div class="space-y-3">
                @if($canManage)
                    <div class="flex justify-end mb-3">
                        <a href="{{ route('courses.roster', $course) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                            {{ __('Manage Students') }}
                        </a>
                    </div>
                @endif

                <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-surface-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white">{{ __('Enrolled Students') }}</h3>
                        <span class="text-xs text-gray-500">{{ count($course->students ?? []) }} {{ __('enrolled') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-surface-700">
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Program') }}</th>
                                    @if($isAdmin)
                                        <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-700">
                                @forelse(($course->students ?? []) as $student)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-medium text-white">{{ $student->name ?? __('Unknown') }}</span>
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
                                        @if($isAdmin)
                                            <td class="px-5 py-3.5 text-right">
                                                <form method="POST" action="{{ route('courses.roster.remove', [$course, $student]) }}" onsubmit="return confirm('Remove {{ $student->name }} from this course?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">{{ __('Remove') }}</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isAdmin ? 4 : 3 }}" class="px-5 py-12 text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                                            </div>
                                            <p class="text-gray-400 text-sm">{{ __('No students enrolled yet.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($isAdmin)
                    @php $studentOptions = $availableStudents->map(fn($s) => ['id' => (string)$s->id, 'label' => $s->name . ' (' . $s->email . ')'])->values(); @endphp
                    <div x-data="{
                        search: '',
                        open: false,
                        selected: [],
                        items: {{ json_encode($studentOptions) }},
                        get filtered() {
                            return this.items.filter(s => s.label.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }" class="bg-surface-800 border border-surface-700 rounded-xl p-5">
                        <h3 class="text-sm font-semibold text-white mb-3">{{ __('Enroll Students') }}</h3>
                        <form method="POST" action="{{ route('courses.roster.add', $course) }}">
                            @csrf
                            <div class="relative mb-3">
                                <input type="text" x-model="search" @click="open = true" @input="open = true"
                                       placeholder="{{ __('Search students...') }}"
                                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                                <div x-show="open" @click.outside="open = false" x-cloak
                                     class="absolute z-10 mt-1 w-full bg-surface-700 border border-surface-600 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                                    <template x-for="student in filtered" :key="student.id">
                                        <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-600 cursor-pointer border-b border-surface-600 last:border-0">
                                            <input type="checkbox" x-model="selected" :value="student.id"
                                                   class="rounded bg-surface-600 border-surface-500 text-brand-500 focus:ring-brand-500/50">
                                            <span class="text-sm text-gray-200" x-text="student.label"></span>
                                        </label>
                                    </template>
                                    <p x-show="filtered.length === 0" class="text-sm text-gray-500 px-3 py-4 text-center">{{ __('No students found.') }}</p>
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
                                    <span x-text="`{{ __('Enroll') }} (${selected.length})`"></span>
                                </button>
                                <button type="button" @click="selected = []; search = ''" x-show="selected.length > 0" class="text-xs text-gray-400 hover:text-white transition-colors">{{ __('Clear') }}</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @break


        @case('grades')
            <div class="space-y-3">
                @if($canManage)
                    <div class="flex justify-end mb-3">
                        <a href="{{ route('courses.grade-rules.index', $course) }}"
                           class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            {{ __('Manage Grade Rules') }}
                        </a>
                    </div>
                @endif

                <div class="bg-surface-800 border border-surface-700 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-white mb-3">{{ __('Grade Rules') }}</h3>
                    @php $totalWeight = $course->gradeRules->sum('weight'); @endphp
                    @forelse(($course->gradeRules ?? collect()) as $rule)
                        <div class="flex items-center justify-between py-2 border-b border-surface-700 last:border-0">
                            <span class="text-sm text-gray-300">{{ ucfirst($rule->category) }}</span>
                            <span class="text-sm font-medium text-white">{{ $rule->weight }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No grade rules configured yet.') }}</p>
                    @endforelse
                    @if($course->gradeRules->isNotEmpty())
                        <div class="flex items-center justify-between py-2 mt-2">
                            <span class="text-sm font-semibold text-white">{{ __('Total') }}</span>
                            <span class="text-sm font-semibold {{ $totalWeight == 100 ? 'text-emerald-400' : 'text-red-400' }}">{{ $totalWeight }}%</span>
                        </div>
                    @endif
                </div>
            </div>
        @break

        @case('rubrics')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.rubrics.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Create Rubric') }}
                    </a>
                </div>
                @if(count($rubrics) === 0)
                    <div class="bg-surface-800 border border-surface-700 rounded-xl p-12 text-center">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-gray-400 text-lg mb-2">{{ __('No rubrics yet') }}</p>
                        <p class="text-gray-500 text-sm">{{ __('Create rubrics to standardize grading across assignments.') }}</p>
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($rubrics as $rubric)
                            @php
                                $criteriaCount = count($rubric->criteria ?? []);
                                $levelsCount = count($rubric->levels ?? []);
                            @endphp
                            <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 hover:border-brand-500/50 transition-all duration-200 group">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors">{{ $rubric->title }}</h3>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        {{ $criteriaCount }} {{ __('criteria') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                        {{ $levelsCount }} {{ __('levels') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 pt-3 border-t border-surface-700">
                                    <a href="{{ route('courses.rubrics.edit', [$course, $rubric]) }}"
                                       class="text-xs text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('courses.rubrics.destroy', [$course, $rubric]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this rubric?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @break

        @case('question-bank')
            <div class="space-y-3">
                <div class="flex justify-end mb-3">
                    <a href="{{ route('courses.question-bank.create', $course) }}"
                       class="flex items-center gap-1.5 text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Question') }}
                    </a>
                </div>
                <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-surface-700">
                        <h3 class="text-sm font-semibold text-white">{{ __('Question Banks') }} ({{ ($questionBanks ?? collect())->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-surface-700">
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Bank') }}</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Type') }}</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Question') }}</th>
                                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Points') }}</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-700">
                                @forelse(($questionBanks ?? collect()) as $bank)
                                    @foreach($bank->items as $item)
                                        <tr class="hover:bg-surface-700/50 transition-colors">
                                            <td class="px-5 py-3.5">
                                                <span class="text-xs text-gray-500">{{ $bank->name }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
                                                    {{ $item->type === 'multiple_choice' ? 'bg-brand-500/10 text-brand-400' : '' }}
                                                    {{ $item->type === 'true_false' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                                    {{ $item->type === 'short_answer' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                                    {{ $item->type === 'long_answer' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                                                    {{ str_replace('_', ' ', ucfirst($item->type)) }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-sm text-gray-300 max-w-md truncate">{{ $item->question }}</td>
                                            <td class="px-5 py-3.5 text-center text-sm text-gray-400">{{ $item->points }}</td>
                                            <td class="px-5 py-3.5 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('courses.question-bank.edit', [$course, $item]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">{{ __('Edit') }}</a>
                                                    <form method="POST" action="{{ route('courses.question-bank.destroy', [$course, $item]) }}" class="inline" onsubmit="return confirm('Delete this question?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">{{ __('Delete') }}</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-12 text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-gray-400 text-sm">{{ __('No question banks yet.') }}</p>
                                            <p class="text-gray-500 text-xs mt-1">{{ __('Add questions that can be reused across quizzes.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @break
    @endswitch
</x-layouts.dashboard>
