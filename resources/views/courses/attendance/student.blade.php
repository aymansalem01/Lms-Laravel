<x-layouts.dashboard>
    <x-slot name="title">{{ __('My Attendance') }} — {{ $course->title }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('My Attendance') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">&larr; {{ __('Back to Course') }}</a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white">{{ $stats['attendanceRate'] }}%</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Attendance Rate') }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-red-400">{{ $stats['absenceRate'] }}%</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Absence Rate') }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-green-400">{{ $stats['present'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Present') }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-red-400">{{ $stats['absent'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Absent') }}</p>
        </div>
        <div class="bg-surface-800 border border-white/10 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['late'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('Late') }}</p>
        </div>
    </div>

    {{-- Warnings --}}
    @if($warnings->isNotEmpty())
        <div class="space-y-2 mb-6">
            @foreach($warnings as $warning)
                <div class="flex items-center gap-3 p-4 rounded-xl {{ $warning->warning_level === 2 ? 'bg-red-500/10 border border-red-500/20' : 'bg-amber-500/10 border border-amber-500/20' }}">
                    <svg class="w-5 h-5 {{ $warning->warning_level === 2 ? 'text-red-400' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        <p class="text-sm font-semibold {{ $warning->warning_level === 2 ? 'text-red-400' : 'text-amber-400' }}">
                            {{ $warning->warning_level === 1 ? __('First Warning') : __('Second Warning') }}
                        </p>
                        <p class="text-xs text-gray-400">{{ __('Absence rate') }}: {{ $warning->absence_rate }}% — {{ $warning->generated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Records --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-surface-700/50">
                        <th class="text-left px-5 py-3 text-gray-400 font-medium">{{ __('Date') }}</th>
                        <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($records as $record)
                        <tr class="hover:bg-surface-700/30 transition-colors">
                            <td class="px-5 py-3 text-white">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
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
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-10 text-center text-gray-500">{{ __('No attendance records yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.dashboard>
