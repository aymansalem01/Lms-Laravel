<x-layouts.dashboard>
    <x-slot name="title">{{ __('Attendance Report') }} — {{ $course->title }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Attendance Report') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('courses.attendance.warnings', $course) }}">
                @csrf
                <button type="submit" class="text-sm bg-amber-600 hover:bg-amber-500 text-white rounded-xl px-5 py-2.5 font-medium transition-colors">
                    {{ __('Generate Warnings') }}
                </button>
            </form>
            <a href="{{ route('courses.attendance.index', $course) }}" class="text-sm bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-5 py-2.5 font-medium transition-colors">
                {{ __('Manage Attendance') }}
            </a>
            <a href="{{ route('courses.attendance.export-report', array_merge(['course' => $course->id], $selectedMonth ? ['month' => $selectedMonth] : [])) }}"
               class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">&larr; {{ __('Back') }}</a>
        </div>
    </div>

    {{-- Month Filter --}}
    <div class="flex items-center gap-3 mb-6">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm text-gray-400">{{ __('Filter by month') }}</label>
            <select name="month" onchange="this.form.submit()"
                    class="bg-surface-700 border border-white/10 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                <option value="">{{ __('All time') }}</option>
                @foreach($months as $ym)
                    <option value="{{ $ym }}" {{ $selectedMonth === $ym ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('F Y') }}
                    </option>
                @endforeach
            </select>
            @if($selectedMonth)
                <a href="{{ route('courses.attendance.report', $course) }}" class="text-xs text-gray-400 hover:text-white transition-colors">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    {{-- Student Attendance Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-surface-700/50">
                        <th class="text-left px-4 py-3 text-gray-400 font-medium">{{ __('Student') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Total') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Present') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Absent') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Late') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Excused') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Absence %') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Attendance %') }}</th>
                        <th class="text-center px-3 py-3 text-gray-400 font-medium">{{ __('Warnings') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($reportData as $row)
                        <tr class="hover:bg-surface-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-xs font-bold text-brand-300">
                                        {{ strtoupper(substr($row['name'], 0, 1)) }}
                                    </div>
                                    <span class="text-white font-medium">{{ $row['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center px-3 py-3 text-gray-300">{{ $row['total'] }}</td>
                            <td class="text-center px-3 py-3 text-green-400 font-medium">{{ $row['present'] }}</td>
                            <td class="text-center px-3 py-3 text-red-400 font-medium">{{ $row['absent'] }}</td>
                            <td class="text-center px-3 py-3 text-yellow-400 font-medium">{{ $row['late'] }}</td>
                            <td class="text-center px-3 py-3 text-blue-400 font-medium">{{ $row['excused'] }}</td>
                            <td class="text-center px-3 py-3 {{ $row['absenceRate'] >= 35 ? 'text-red-400 font-bold' : ($row['absenceRate'] >= 20 ? 'text-amber-400 font-bold' : 'text-gray-300') }}">
                                {{ $row['absenceRate'] }}%
                            </td>
                            <td class="text-center px-3 py-3 {{ $row['attendanceRate'] < 80 ? 'text-red-400' : 'text-green-400' }}">
                                {{ $row['attendanceRate'] }}%
                            </td>
                            <td class="text-center px-3 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    @if($row['warnings']->contains(2))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">2nd</span>
                                    @elseif($row['warnings']->contains(1))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">1st</span>
                                    @else
                                        <span class="text-gray-600">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-gray-500">{{ __('No students enrolled.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monthly Breakdown --}}
    @if($months->isNotEmpty())
        <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-white/10">
                <h3 class="text-sm font-semibold text-white">{{ __('Monthly Breakdown') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-surface-700/50">
                            <th class="text-left px-4 py-3 text-gray-400 font-medium">{{ __('Student') }}</th>
                            @foreach($months as $ym)
                                <th class="text-center px-2 py-3 text-gray-400 font-medium text-xs">{{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('M Y') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($reportData as $row)
                            @php $ms = $monthlyStats[$row['id']]['months'] ?? []; @endphp
                            <tr class="hover:bg-surface-700/30 transition-colors">
                                <td class="px-4 py-3 text-white font-medium text-xs">{{ $row['name'] }}</td>
                                @foreach($months as $ym)
                                    @php $m = $ms[$ym] ?? ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'attendanceRate' => 0]; @endphp
                                    <td class="text-center px-2 py-3">
                                        @if($m['total'] > 0)
                                            <div class="text-xs">
                                                <span class="text-green-400 font-medium">{{ $m['present'] }}</span>
                                                <span class="text-gray-600">/</span>
                                                <span class="text-red-400">{{ $m['absent'] }}</span>
                                                @if($m['late'] > 0)<span class="text-gray-500"> L{{ $m['late'] }}</span>@endif
                                                @if($m['excused'] > 0)<span class="text-gray-500"> E{{ $m['excused'] }}</span>@endif
                                            </div>
                                            <div class="text-[10px] {{ $m['attendanceRate'] < 80 ? 'text-red-400' : 'text-green-400' }}">{{ $m['attendanceRate'] }}%</div>
                                        @else
                                            <span class="text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Issued Warnings Log --}}
    @if($courseWarnings->isNotEmpty())
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">{{ __('Warning Log') }}</h3>
            <div class="space-y-2">
                @foreach($courseWarnings as $warning)
                    <div class="flex items-center justify-between p-3 rounded-lg {{ $warning->warning_level === 2 ? 'bg-red-500/5 border border-red-500/10' : 'bg-amber-500/5 border border-amber-500/10' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ $warning->warning_level === 2 ? 'text-red-400' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div>
                                <p class="text-sm text-white">{{ $warning->student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $warning->warning_level === 1 ? __('First Warning') : __('Second Warning') }} &middot; {{ __('Absence') }}: {{ $warning->absence_rate }}% &middot; {{ $warning->generated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.dashboard>
