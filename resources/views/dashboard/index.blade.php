<x-layouts.dashboard>
    <x-slot name="title">{{ __('messages.dashboard') }}</x-slot>

    {{-- ── WELCOME SECTION ─────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white poppins">{{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}</h1>
        <p class="text-xl font-black gradient-text poppins leading-tight">Create. Learn. Inspire.</p>
    </div>

    {{-- ── KPI GRID ─────────────────────────────────────────────────── --}}
    @php
        $greens = ['#10B981','#059669'];
        $oranges = ['#F59E0B','#D97706'];
        $blues = ['#3B82F6','#2563EB'];
        $purples = ['#8B5CF6','#7C3AED'];
        $ringColors = ['#FF4D6D','#A6E22E','#4E7BFF','#FFC83D'];
        $kpiBg = ['rgba(255,77,109,0.30)','rgba(166,226,46,0.30)','rgba(78,123,255,0.30)','rgba(255,200,61,0.30)'];
        $kpiIcons = [
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>',
            'courses' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
            'submissions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            'pending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
            'progress' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
            'students' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>',
            'avg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
            'sessions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
            'graded' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ];
        $circumference = 2 * 3.14159 * 18;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
        @if(auth()->user()->role === 'admin')
            {{-- Admin KPI 1: Total Users w/ ring --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Total Users') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[0] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[0] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['users'] !!}</svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-black text-white poppins">{{ $totalUsers ?? 0 }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Registered users') }}</p>
                    </div>
                </div>
            </div>
            {{-- Admin KPI 2: Total Courses w/ badge --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Total Courses') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[1] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[1] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['courses'] !!}</svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-black text-white poppins">{{ $totalCourses ?? 0 }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Available courses') }}</p>
                    </div>
                </div>
            </div>
            {{-- Admin KPI 3: Submissions --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Submissions') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[2] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[2] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['submissions'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ $totalSubmissions ?? 0 }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Total submissions') }}</p>
                <a href="{{ route('admin.grading.index') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('Review all') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            {{-- Admin KPI 4: Pending Grades --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Pending Grades') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[3] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[3] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['pending'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ $pendingGrades ?? 0 }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Awaiting grading') }}</p>
                <a href="{{ route('admin.grading.index') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('Grade now') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @elseif(auth()->user()->role === 'instructor')
            {{-- Instructor KPI 1: My Courses w/ ring --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('My Courses') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[0] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[0] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['courses'] !!}</svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-white poppins">{{ $myCoursesCount ?? 0 }}</p>
                    <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Active courses') }}</p>
                </div>
            </div>
            {{-- Instructor KPI 2: Total Students w/ badge --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Total Students') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[1] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[1] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['students'] !!}</svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-black text-white poppins">{{ $totalStudents ?? 0 }}</p>
                    <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Enrolled across courses') }}</p>
                </div>
            </div>
            {{-- Instructor KPI 3: Pending Grading --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Pending Grading') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[2] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[2] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['pending'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ $pendingGrading ?? 0 }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Submissions to grade') }}</p>
                <a href="{{ route('grading.index') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('Grade now') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            {{-- Instructor KPI 4: Graded --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Graded') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[3] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[3] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['graded'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ ($recentSubmissions ?? collect())->count() }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Completed grades') }}</p>
                <a href="{{ route('grading.index') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('View all') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @else
            {{-- Student KPI 1: Overall Progress w/ ring --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Overall Progress') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[0] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[0] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['progress'] !!}</svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-black text-white poppins">{{ ($recentGrades ?? collect())->avg('score') ? number_format(($recentGrades ?? collect())->avg('score'), 0) : '—' }}%</p>
                        <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Overall average') }}</p>
                    </div>
                    <div class="relative w-16 h-16">
                        @php $progressPct = min(($recentGrades ?? collect())->avg('score') ?? 0, 100); @endphp
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 44 44">
                            <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="3"/>
                            <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $ringColors[0] }}" stroke-width="3" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference * (1 - $progressPct / 100) }}" class="progress-ring-circle"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-[11px] font-bold text-white poppins">{{ number_format($progressPct, 0) }}%</span>
                    </div>
                </div>
            </div>
            {{-- Student KPI 2: Avg Grade w/ badge --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Average Grade') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[1] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[1] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['avg'] !!}</svg>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-black text-white poppins">{{ $avgScore ?? '—' }}%</p>
                        <p class="text-[11px] text-gray-500 mt-1 poppins">{{ __('Across all courses') }}</p>
                    </div>
                    @if(($avgScore ?? 0) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ ($avgScore ?? 0) >= 70 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' }} poppins">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        {{ ($avgScore ?? 0) >= 70 ? __('Good') : __('Needs work') }}
                    </span>
                    @endif
                </div>
            </div>
            {{-- Student KPI 3: Enrolled Courses --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Enrolled Courses') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[2] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[2] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['courses'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ ($enrolledCourses ?? collect())->count() }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Currently enrolled') }}</p>
                <a href="{{ route('courses.catalog') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('Browse catalog') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            {{-- Student KPI 4: Upcoming Sessions --}}
            <div class="kpi-card relative overflow-hidden rounded-2xl p-5 shadow-sm border border-white/10 bg-surface-800">
                <div class="flex items-start justify-between mb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-500 poppins">{{ __('Upcoming Sessions') }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $kpiBg[3] }};">
                        <svg class="w-5 h-5" style="color: {{ $ringColors[3] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $kpiIcons['sessions'] !!}</svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white poppins mb-1">{{ ($upcomingEvents ?? collect())->count() }}</p>
                <p class="text-[11px] text-gray-500 poppins">{{ __('Scheduled events') }}</p>
                <a href="{{ route('live.index') }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-medium text-brand-500 hover:text-brand-400 poppins">
                    {{ __('View schedule') }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>

    {{-- ── BOTTOM GRID: 65/35 SPLIT ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-[65%_35%] gap-8 mb-10">
        {{-- ── LEFT COLUMN (65%) ──────────────────────────────────── --}}
        <div class="space-y-10">
            {{-- ── CONTINUE LEARNING CAROUSEL ─────────────────────── --}}
            @if(auth()->user()->role !== 'admin')
            <section>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-white poppins">{{ __('Continue Learning') }}</h2>
                        <p class="text-xs text-gray-500 poppins">{{ __('Pick up where you left off') }}</p>
                    </div>
                    @php $carouselRef = auth()->user()->role === 'instructor' ? 's' : 's2'; @endphp
                    @if((auth()->user()->role === 'instructor' ? ($myCourses ?? collect())->count() : ($enrolledCourses ?? collect())->count()) > 0)
                    <div class="flex gap-1">
                        <button @click="$refs.{{ $carouselRef }}.scrollBy({ left: -400, behavior: 'smooth' })"
                                class="w-8 h-8 rounded-full bg-surface-700 border border-white/10 flex items-center justify-center text-gray-500 hover:text-gray-800 dark:hover:text-white hover:bg-surface-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="$refs.{{ $carouselRef }}.scrollBy({ left: 400, behavior: 'smooth' })"
                                class="w-8 h-8 rounded-full bg-surface-700 border border-white/10 flex items-center justify-center text-gray-500 hover:text-gray-800 dark:hover:text-white hover:bg-surface-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    @endif
                </div>
                <div x-ref="{{ $carouselRef }}" class="flex gap-4 overflow-x-auto overflow-y-hidden scroll-smooth max-w-full scrollbar-hide pb-2">
                    @php $courses = auth()->user()->role === 'instructor' ? ($myCourses ?? collect()) : ($enrolledCourses ?? collect()); @endphp
                    @forelse($courses as $course)
                    <a href="{{ route('courses.show', $course) }}" class="course-card shrink-0 w-64 group relative overflow-hidden rounded-2xl border border-white/10 bg-surface-800 shadow-sm">
                        {{-- Thumbnail --}}
                        @if($course->cover_image_url)
                            <div class="relative h-36 bg-cover bg-center" style="background-image: url('{{ $course->cover_image_url }}')"></div>
                        @else
                            <div class="relative h-36 flex items-center justify-center" style="background: linear-gradient(135deg, {{ $ringColors[$loop->index % 4] }}33, transparent);">
                                <span class="text-4xl font-bold text-white/30 poppins">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                            </div>
                        @endif
                        {{-- Category badge --}}
                        <span class="absolute top-3 left-3 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-white/90 dark:bg-surface-900/90 text-brand-600 dark:text-brand-400 shadow-sm poppins">
                            {{ $course->program ?? __('In Progress') }}
                        </span>
                        {{-- Card body --}}
                        <div class="p-4">
                            <h3 class="font-bold text-white text-sm leading-snug poppins line-clamp-1">{{ $course->title }}</h3>
                            <p class="text-[11px] text-gray-500 mt-1 poppins">{{ $course->instructor->name ?? __('Self-paced') }}</p>
                            {{-- Progress bar --}}
                            @php $progress = $course->pivot->progress ?? $course->progress ?? 0; @endphp
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] text-gray-500 poppins">{{ __('Progress') }}</span>
                                    <span class="text-[10px] font-semibold text-white/70 poppins">{{ $progress }}%</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ $progress }}%; background: linear-gradient(90deg, {{ $ringColors[$loop->index % 4] }}, {{ $ringColors[($loop->index + 1) % 4] }});"></div>
                                </div>
                            </div>
                            {{-- Lesson count --}}
                            <p class="text-[11px] text-gray-500 mt-3 poppins flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                {{ $course->lessons_count ?? $course->assignments_count ?? 0 }}/{{ $course->total_lessons ?? $course->sections_count ?? 0 }} {{ __('lessons') }}
                            </p>
                        </div>
                    </a>
                    @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center w-full">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-gray-500 text-sm poppins">{{ auth()->user()->role === 'instructor' ? __("You haven't created any courses yet.") : __("You haven't enrolled in any courses yet.") }}</p>
                        @if(auth()->user()->role === 'student')
                        <a href="{{ route('courses.catalog') }}" class="text-brand-500 hover:text-brand-400 text-sm poppins mt-1 inline-block">{{ __('Browse the course catalog') }}</a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </section>
            @endif

            {{-- ── RECENT ANNOUNCEMENTS ───────────────────────────── --}}
            <section>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-white poppins">{{ __('Recent Announcements') }}</h2>
                        <p class="text-xs text-gray-500 poppins">{{ __('Latest updates from your instructors') }}</p>
                    </div>
                    @if(($announcements ?? collect())->count() > 0)
                    <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-400 poppins">{{ __('View all') }} &rarr;</a>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse(($announcements ?? collect()) as $announcement)
                    <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-surface-800 p-4 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500/20 to-coral-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-white text-sm poppins">{{ $announcement->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1 poppins line-clamp-2">{{ $announcement->content }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium uppercase tracking-wider bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-500/20 shrink-0 poppins">{{ __('New') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-2 poppins flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $announcement->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 poppins">{{ __('No announcements yet') }}</p>
                    </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- ── RIGHT COLUMN (35%) ─────────────────────────────────── --}}
        <div>
            {{-- ── CALENDAR WIDGET ───────────────────────────────── --}}
            @php
                $today = now();
                $firstDay = $today->copy()->startOfMonth();
                $lastDay = $today->copy()->endOfMonth();
                $startOfGrid = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $endOfGrid = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                $todayStr = $today->format('Y-m-d');
                $month = $today->month;
                $eventDates = ($upcomingEvents ?? collect())->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique();
                $dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                $dayNamesAr = ['أحد','اثن','ثلث','أرب','خمس','جمع','سبت'];
                $days = app()->getLocale() === 'ar' ? $dayNamesAr : $dayNames;
            @endphp
            <section class="sticky top-24">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-white poppins">{{ __('Calendar') }}</h2>
                        <p class="text-xs text-gray-500 poppins">{{ $today->format('F Y') }}</p>
                    </div>
                    <a href="{{ route('assignments.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-400 poppins">{{ __('All events') }} &rarr;</a>
                </div>
                <div class="rounded-2xl border border-white/10 bg-surface-800 p-4 shadow-sm">
                    {{-- Day headers --}}
                    <div class="grid grid-cols-7 gap-px mb-2">
                        @foreach($days as $day)
                            <div class="text-[10px] font-semibold text-gray-500 text-center py-1 uppercase tracking-wider poppins">{{ $day }}</div>
                        @endforeach
                    </div>
                    {{-- Calendar grid --}}
                    <div class="grid grid-cols-7 gap-px">
                        @php $cell = $startOfGrid->copy(); @endphp
                        @while($cell <= $endOfGrid)
                            @php
                                $dateStr = $cell->format('Y-m-d');
                                $isToday = $dateStr === $todayStr;
                                $isCurrentMonth = $cell->month === $month;
                                $hasEvent = $isCurrentMonth && $eventDates->contains($dateStr);
                                $eventTypes = $isCurrentMonth ? ($upcomingEvents ?? collect())->filter(fn($e) => \Carbon\Carbon::parse($e['date'])->format('Y-m-d') === $dateStr)->pluck('type') : collect();
                            @endphp
                            <div class="text-center py-1 {{ $isCurrentMonth ? '' : 'opacity-20' }}">
                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs relative
                                    {{ $isToday ? 'bg-brand-500 text-white font-bold' : ($hasEvent ? 'text-white font-semibold' : 'text-gray-500') }} poppins">
                                    {{ $cell->day }}
                                </div>
                                @if($hasEvent && !$isToday)
                                    <div class="flex items-center justify-center gap-0.5 mt-0.5">
                                        @foreach($eventTypes->take(3) as $type)
                                            <span class="calendar-event-dot {{ $type === 'live_session' ? 'session' : ($type === 'quiz' ? 'quiz' : 'assignment') }}"></span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @php $cell->addDay(); @endphp
                        @endwhile
                    </div>
                    {{-- Legend --}}
                    <div class="flex items-center justify-center gap-4 mt-4 pt-3 border-t border-white/10">
                        <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-500 poppins">
                            <span class="calendar-event-dot assignment"></span> {{ __('Due') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-500 poppins">
                            <span class="calendar-event-dot session"></span> {{ __('Sessions') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-500 poppins">
                            <span class="calendar-event-dot quiz"></span> {{ __('Quizzes') }}
                        </span>
                    </div>
                </div>
                {{-- Upcoming events mini-list --}}
                @if(($upcomingEvents ?? collect())->count() > 0)
                <div class="mt-4 space-y-2">
                    @foreach(($upcomingEvents ?? collect())->take(4) as $event)
                    <a href="{{ $event['route'] }}" class="flex items-center gap-3 rounded-xl border border-white/10 bg-surface-800 p-3 hover:bg-surface-700 transition-all shadow-sm">
                        <div class="w-2 h-2 rounded-full {{ $event['type'] === 'live_session' ? 'bg-blue-500' : ($event['type'] === 'quiz' ? 'bg-amber-500' : 'bg-brand-500') }} shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white poppins truncate">{{ $event['title'] }}</p>
                            <p class="text-[10px] text-gray-500 poppins">{{ \Carbon\Carbon::parse($event['date'])->format('M d, g:i A') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium uppercase tracking-wider poppins
                            {{ $event['type'] === 'live_session' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' : ($event['type'] === 'quiz' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400') }}">
                            {{ $event['label'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="mt-4 rounded-xl border border-dashed border-white/10 p-6 text-center">
                    <p class="text-xs text-gray-500 poppins">{{ __('No upcoming events') }}</p>
                </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.dashboard>