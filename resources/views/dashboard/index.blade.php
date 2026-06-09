<x-layouts.dashboard>
    <x-slot name="title">{{ __('messages.dashboard') }}</x-slot>

    {{-- Welcome Header --}}
    <div class="flex items-start justify-between gap-4 mb-10">
        <div>
            <p class="section-label flex items-center gap-2 mb-2">
                <span class="h-px w-8 bg-brand-500 inline-block"></span>
                @switch(auth()->user()->role)
                    @case('student'){{ __('Your Learning Journey') }}@break
                    @case('instructor'){{ __('Instructor Overview') }}@break
                    @default{{ __('Platform Control') }}
                @endswitch
            </p>
            <h1 class="text-3xl font-black text-white tracking-tight leading-tight">
                @switch(auth()->user()->role)
                    @case('student'){{ __('messages.welcome_student', ['name' => auth()->user()->name]) }}@break
                    @case('instructor'){{ __('messages.welcome_instructor', ['name' => auth()->user()->name]) }}@break
                    @default{{ __('messages.welcome_admin') }}
                @endswitch
            </h1>
        </div>
    </div>

    {{-- ── ADMIN DASHBOARD ──────────────────────────────────────── --}}
    @if(auth()->user()->role === 'admin')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-brand-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Total Users') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-brand-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $totalUsers ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-coral-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Total Courses') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-coral-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-coral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $totalCourses ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Submissions') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $totalSubmissions ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-amber-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Pending Grades') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $pendingGrades ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-bold text-white mb-4">{{ __('Announcements') }}</h2>
        @forelse(($announcements ?? collect()) as $announcement)
        <div class="relative overflow-hidden rounded-2xl border border-brand-500/20 bg-surface-800 p-5 mb-3">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-r from-brand-500/10 to-transparent opacity-60 pointer-events-none"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-white">{{ $announcement->title }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $announcement->content }}</p>
                        <p class="text-xs text-gray-600 mt-2">{{ $announcement->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
            <p class="text-sm font-mono text-gray-600 uppercase tracking-wider">{{ __('No announcements yet') }}</p>
        </div>
        @endforelse
    </div>

    @endif

    {{-- ── INSTRUCTOR DASHBOARD ─────────────────────────────────── --}}
    @if(auth()->user()->role === 'instructor')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-brand-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('My Courses') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-brand-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $myCoursesCount ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-coral-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Total Students') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-coral-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-coral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $totalStudents ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-amber-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Pending Grading') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $pendingGrading ?? 0 }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Graded') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ $pendingGrading ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Your Courses — editorial split --}}
    <section class="mb-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-brand-500 inline-block"></span>
                    {{ __('My Courses') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    Your<br><span class="gradient-text">Courses.</span>
                </h2>
                <p class="text-sm font-mono text-gray-500">{{ $myCoursesCount ?? 0 }} active</p>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('courses.create') }}" class="btn-primary inline-flex items-center gap-1.5 text-xs px-4 py-2">
                    {{ __('Create Course') }}
                </a>
                @endif
            </div>
            <div class="flex-1 min-w-0 space-y-3">
                @forelse(($myCourses ?? collect())->take(6) as $course)
                <a href="{{ route('courses.show', $course) }}" class="group relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 card-hover p-5 flex items-center gap-4 block">
                    <div aria-hidden class="absolute inset-0 bg-gradient-to-r from-brand-500/10 to-transparent opacity-60 pointer-events-none"></div>
                    @if($course->cover_image_url)
                        <div class="relative w-14 h-14 rounded-xl shrink-0 overflow-hidden bg-cover bg-center" style="background-image: url('{{ $course->cover_image_url }}')"></div>
                    @else
                        <div class="relative w-14 h-14 rounded-xl bg-surface-700 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-brand-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                    <div class="relative flex-1 min-w-0">
                        <p class="font-bold text-white text-base leading-snug line-clamp-1">{{ $course->title }}</p>
                        <p class="text-[11px] font-mono text-gray-500 mt-0.5 uppercase tracking-wider">
                            {{ $course->enrollments_count ?? 0 }} {{ __('students') }}
                        </p>
                    </div>
                    <svg class="relative w-4 h-4 text-gray-600 group-hover:text-brand-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @empty
                <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-surface-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-gray-400 text-sm mb-1">{{ __("You haven't created any courses yet.") }}</p>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('courses.create') }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">{{ __('Create your first course') }}</a>
                    @endif
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Grade Queue — editorial split --}}
    <section>
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-amber-500 inline-block"></span>
                    {{ __('Grade Queue') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    Grade<br><span class="gradient-text">Queue.</span>
                </h2>
                <p class="text-sm font-mono text-gray-500">{{ $pendingGrading ?? 0 }} pending</p>
                <a href="{{ route('grading.index') }}" class="inline-flex items-center gap-1.5 text-xs font-mono uppercase tracking-wider text-gray-500 hover:text-white transition-colors">
                    {{ __('All submissions') }} &rarr;
                </a>
            </div>
            <div class="flex-1 min-w-0 space-y-3">
                @forelse(($recentSubmissions ?? collect()) as $submission)
                <a href="{{ route('grading.show', $submission) }}" class="group relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 card-hover p-4 block">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-brand-500/20 flex items-center justify-center text-sm font-bold text-brand-400 shrink-0">
                            {{ strtoupper(substr($submission->student->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-white text-sm truncate">{{ $submission->student->name ?? __('Unknown') }}</p>
                            <p class="text-[11px] font-mono uppercase tracking-wider text-gray-500 truncate mt-0.5">{{ $submission->assignment->title ?? '' }}</p>
                        </div>
                        @if(!$submission->grade)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 flex-shrink-0">{{ __('Pending') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex-shrink-0">{{ __('Graded') }}</span>
                        @endif
                    </div>
                    <div class="mt-3 pt-2 border-t border-white/5">
                        <span class="text-[11px] font-mono text-gray-600 flex items-center gap-1 uppercase tracking-wider">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $submission->created_at->diffForHumans() }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
                    <p class="text-sm font-mono text-gray-600 uppercase tracking-wider">{{ __('No submissions yet') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Upcoming Events (Moodle-style calendar) --}}
    <section class="mb-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-blue-500 inline-block"></span>
                    {{ __('Upcoming') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    Upcoming<br><span class="gradient-text">Events.</span>
                </h2>
                <p class="text-sm font-mono text-gray-500">{{ ($upcomingEvents ?? collect())->count() }} events</p>
            </div>
            <div class="flex-1 min-w-0">
                <x-upcoming-calendar :events="$upcomingEvents ?? collect()" />
            </div>
        </div>
    </section>
    @endif

    {{-- ── STUDENT DASHBOARD ────────────────────────────────────── --}}
    @if(auth()->user()->role === 'student')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-brand-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Enrolled Courses') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-brand-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ ($enrolledCourses ?? collect())->count() }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-coral-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Assignments Done') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-coral-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-coral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ ($recentSubmissions ?? collect())->count() }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Avg Grade') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ ($avgScore ?? '—') }}%</p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 p-5">
            <div aria-hidden class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-transparent opacity-80 pointer-events-none"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-mono uppercase tracking-widest text-gray-500 leading-tight max-w-[120px]">{{ __('Upcoming Sessions') }}</p>
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <p class="text-4xl font-black text-white tracking-tight leading-none">{{ ($upcomingLiveSessions ?? collect())->count() }}</p>
            </div>
        </div>
    </div>

    {{-- My Courses — editorial split --}}
    <section class="mb-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-brand-500 inline-block"></span>
                    {{ __('My Courses') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    My<br><span class="gradient-text">Courses.</span>
                </h2>
                <p class="text-sm font-mono text-gray-500">{{ ($enrolledCourses ?? collect())->count() }} enrolled</p>
                <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-1.5 text-xs font-mono uppercase tracking-wider text-gray-500 hover:text-white transition-colors">
                    {{ __('Browse Catalog') }} &rarr;
                </a>
            </div>
            <div class="flex-1 min-w-0">
                @forelse(($enrolledCourses ?? collect()) as $course)
                <a href="{{ route('courses.show', $course) }}" class="group relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800 card-hover mb-4 block">
                    @if($course->cover_image_url)
                        <div class="h-32 bg-cover bg-center" style="background-image: url('{{ $course->cover_image_url }}')"></div>
                    @else
                        <div class="h-32 gb flex items-center justify-center">
                            <span class="text-3xl font-bold text-white/50">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="relative p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-white text-lg leading-snug">{{ $course->title }}</h3>
                                <p class="text-[11px] font-mono uppercase tracking-wider text-gray-500 mt-0.5">{{ $course->instructor->name ?? __('Instructor') }}</p>
                            </div>
                            @if($course->program)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border border-brand-500/30 text-brand-400 bg-brand-500/10 flex-shrink-0">{{ $course->program }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-surface-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-gray-400 text-sm mb-1">{{ __("You haven't enrolled in any courses yet.") }}</p>
                    <a href="{{ route('courses.catalog') }}" class="text-brand-400 hover:text-brand-300 text-sm transition-colors">{{ __('Browse the course catalog') }}</a>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Upcoming Events (Moodle-style calendar) --}}
    <section class="mb-10">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-coral-500 inline-block"></span>
                    {{ __('Upcoming') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    Upcoming<br><span class="gradient-text">Events.</span>
                </h2>
                <p class="text-sm font-mono text-gray-500">{{ ($upcomingEvents ?? collect())->count() }} events</p>
                <a href="{{ route('assignments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-mono uppercase tracking-wider text-gray-500 hover:text-white transition-colors">
                    {{ __('All assignments') }} &rarr;
                </a>
            </div>
            <div class="flex-1 min-w-0">
                <x-upcoming-calendar :events="$upcomingEvents ?? collect()" />
            </div>
        </div>
    </section>
    @endif

    {{-- ── ANNOUNCEMENTS (shared) ───────────────────────────────── --}}
    <section>
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <div class="flex-shrink-0 lg:w-64 xl:w-72 space-y-4">
                <p class="section-label flex items-center gap-2">
                    <span class="h-px w-8 bg-brand-500 inline-block"></span>
                    {{ __('Announcements') }}
                </p>
                <h2 class="text-5xl lg:text-6xl font-black text-white tracking-tight leading-[.9]">
                    Latest<br><span class="gradient-text">Updates.</span>
                </h2>
            </div>
            <div class="flex-1 min-w-0 space-y-3">
                @forelse(($announcements ?? collect()) as $announcement)
                <div class="relative overflow-hidden rounded-2xl border border-brand-500/20 bg-surface-800 p-5">
                    <div aria-hidden class="absolute inset-0 bg-gradient-to-r from-brand-500/10 to-transparent opacity-60 pointer-events-none"></div>
                    <div class="relative flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-500/10 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-white">{{ $announcement->title }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ $announcement->content }}</p>
                            <p class="text-[11px] font-mono text-gray-600 mt-2 uppercase tracking-wider">{{ $announcement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
                    <p class="text-sm font-mono text-gray-600 uppercase tracking-wider">{{ __('No announcements yet') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.dashboard>