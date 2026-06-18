<x-layouts.dashboard>
    <x-slot name="title">Analytics — Luminus LMS</x-slot>

    <h1 class="text-2xl font-bold text-white mb-6">Analytics Dashboard</h1>

    {{-- Stats Cards Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total Users</p>
            <p class="text-3xl font-bold text-white">{{ $totalUsers }}</p>
            <div class="flex gap-2 mt-2 text-xs text-gray-500">
                <span><span class="text-blue-400">{{ $totalStudents }}</span> Students</span>
                <span><span class="text-brand-300">{{ $totalInstructors }}</span> Instructors</span>
                <span><span class="text-purple-400">{{ $totalAdmins }}</span> Admins</span>
            </div>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total Courses</p>
            <p class="text-3xl font-bold text-white">{{ $totalCourses }}</p>
            <div class="flex gap-2 mt-2 text-xs text-gray-500">
                <span><span class="text-emerald-400">{{ $publishedCourses }}</span> Published</span>
                <span><span class="text-gray-400">{{ $draftCourses }}</span> Draft</span>
            </div>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Submissions</p>
            <p class="text-3xl font-bold text-white">{{ $totalSubmissions }}</p>
            <div class="flex gap-2 mt-2 text-xs text-gray-500">
                <span><span class="text-emerald-400">{{ $gradedSubmissions }}</span> Graded</span>
                <span class="text-gray-400">({{ $gradingRate }}%)</span>
            </div>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
            <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Average Score</p>
            <p class="text-3xl font-bold {{ $avgScore >= 70 ? 'text-emerald-400' : 'text-coral-400' }}">{{ number_format($avgScore, 1) }}%</p>
            <p class="text-xs text-gray-500 mt-2">{{ $totalLiveSessions }} Live Sessions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Users by Program --}}
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4">Users by Program</h3>
            @forelse($programBreakdown as $program => $data)
                @php
                    $allCounts = $programBreakdown->pluck('count')->toArray();
                    $maxCount = !empty($allCounts) ? max($allCounts) : 1;
                @endphp
                <div class="mb-3">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-300">{{ $program }}</span>
                        <span class="text-gray-500">{{ $data['count'] }}</span>
                    </div>
                    <div class="w-full h-2 bg-surface-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-coral-500 transition-all" style="width: {{ ($data['count'] / $maxCount) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No program data available.</p>
            @endforelse
        </div>

        {{-- Top Courses by Enrollment --}}
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4">Top Courses by Enrollment</h3>
            @forelse($topCourses as $course)
                @php
                    $maxEnrolled = $topCourses->max('enrollments_count') ?: 1;
                @endphp
                <div class="mb-3">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-300 truncate">{{ $course->title }}</span>
                        <span class="text-gray-500">{{ $course->enrollments_count }}</span>
                    </div>
                    <div class="w-full h-2 bg-surface-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ ($course->enrollments_count / $maxEnrolled) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No enrollment data available.</p>
            @endforelse
        </div>
    </div>

    {{-- Monthly Signups Chart --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-8">
        <h3 class="text-white font-semibold mb-4">Monthly Signups ({{ date('Y') }})</h3>
        @php $maxSignups = max($signupsByMonth) ?: 1; @endphp
        @forelse($signupsByMonth as $month => $count)
            <div class="mb-2">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-gray-300 text-xs">{{ $monthNames[$month] ?? $month }}</span>
                    <span class="text-gray-500 text-xs">{{ $count }}</span>
                </div>
                <div class="w-full h-2 bg-surface-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-coral-500 transition-all" style="width: {{ ($count / $maxSignups) * 100 }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm">No signup data available.</p>
        @endforelse
    </div>

    {{-- Monthly Submissions Chart --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 mb-8">
        <h3 class="text-white font-semibold mb-4">Monthly Submissions ({{ date('Y') }})</h3>
        @php $maxSubmissions = max($submissionsByMonth) ?: 1; @endphp
        @forelse($submissionsByMonth as $month => $count)
            <div class="mb-2">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-gray-300 text-xs">{{ $monthNames[$month] ?? $month }}</span>
                    <span class="text-gray-500 text-xs">{{ $count }}</span>
                </div>
                <div class="w-full h-2 bg-surface-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-coral-400 to-amber-400 transition-all" style="width: {{ ($count / $maxSubmissions) * 100 }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm">No submission data available.</p>
        @endforelse
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4">Recent Enrollments</h3>
            @forelse($recentEnrollments as $e)
                <div class="flex items-start gap-3 py-2 border-b border-white/5 last:border-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-300">{{ $e->student->name ?? 'Unknown' }} → {{ $e->course->title ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($e->enrolled_at)->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No recent enrollments.</p>
            @endforelse
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4">Recent Grades</h3>
            @forelse($recentGrades as $g)
                <div class="flex items-start gap-3 py-2 border-b border-white/5 last:border-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-300">{{ $g->submission->student->name ?? 'Unknown' }} — {{ number_format($g->score ?? 0, 1) }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $g->submission->assignment->title ?? 'Unknown' }} • {{ \Carbon\Carbon::parse($g->graded_at)->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No recent grades.</p>
            @endforelse
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-white font-semibold mb-4">New Users</h3>
            @forelse($recentUsers as $u)
                <div class="flex items-start gap-3 py-2 border-b border-white/5 last:border-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-300">{{ $u->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst($u->role) }} • {{ $u->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No new users.</p>
            @endforelse
        </div>
    </div>
</x-layouts.dashboard>