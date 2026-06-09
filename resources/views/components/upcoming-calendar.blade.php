@php
    $events = $events ?? collect();
    $today = now();
    $firstDay = $today->copy()->startOfMonth();
    $lastDay = $today->copy()->endOfMonth();
    $startOfGrid = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $endOfGrid = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $todayStr = $today->format('Y-m-d');
    $month = $today->month;

    $eventDates = $events->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique();
    $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $dayNamesAr = ['أحد', 'اثن', 'ثلث', 'أرب', 'خمس', 'جمع', 'سبت'];
    $days = app()->getLocale() === 'ar' ? $dayNamesAr : $dayNames;
@endphp

<div class="max-w-2xl mx-auto">
<div class="bg-surface-800/60 backdrop-blur-sm border border-white/5 rounded-2xl p-5 mb-4">
    <div class="flex items-center justify-between mb-3">
        <div class="text-xs font-semibold text-white uppercase tracking-wider">{{ $today->format('F Y') }}</div>
        <div class="flex items-center gap-1">
            <span class="inline-flex items-center gap-1 text-[10px] text-gray-500 font-mono">
                <span class="w-2 h-2 rounded-full bg-coral-400"></span> {{ __('Due') }}
            </span>
            <span class="inline-flex items-center gap-1 text-[10px] text-gray-500 font-mono ml-2">
                <span class="w-2 h-2 rounded-full bg-brand-400"></span> {{ __('Live') }}
            </span>
        </div>
    </div>
    <div class="grid grid-cols-7 gap-px">
        @foreach($days as $day)
            <div class="text-[10px] font-mono text-gray-600 text-center py-1 uppercase tracking-wider">{{ $day }}</div>
        @endforeach
        @php $cell = $startOfGrid->copy(); @endphp
        @while($cell <= $endOfGrid)
            @php
                $dateStr = $cell->format('Y-m-d');
                $isToday = $dateStr === $todayStr;
                $isCurrentMonth = $cell->month === $month;
                $hasEvent = $isCurrentMonth && $eventDates->contains($dateStr);
            @endphp
            <div class="text-center py-1 {{ $isCurrentMonth ? '' : 'opacity-20' }}">
                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs relative
                    {{ $isToday ? 'bg-brand-500 text-white font-bold' : ($hasEvent ? 'text-white font-semibold' : 'text-gray-400') }}">
                    {{ $cell->day }}
                    @if($hasEvent && !$isToday)
                        <span class="absolute -bottom-0.5 w-1 h-1 rounded-full bg-coral-400"></span>
                    @endif
                </div>
            </div>
            @php $cell->addDay(); @endphp
        @endwhile
    </div>
</div>

<div class="space-y-3">
    @forelse($events as $event)
    <a href="{{ $event['route'] }}" class="group relative overflow-hidden rounded-2xl border border-white/5 bg-surface-800/70 backdrop-blur-sm hover:bg-surface-700/80 card-hover p-4 block">
        <div class="flex items-start justify-between gap-2">
            <div class="space-y-1 flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono text-gray-600 uppercase tracking-wider shrink-0">{{ \Carbon\Carbon::parse($event['date'])->format('M d') }}</span>
                    <span class="h-3 w-px bg-white/10"></span>
                    <p class="font-bold text-white text-sm line-clamp-1">{{ $event['title'] }}</p>
                </div>
                <p class="text-[11px] font-mono uppercase tracking-wider text-gray-500 line-clamp-1">{{ $event['course_title'] }}</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider
                @if($event['type'] === 'live_session') bg-blue-500/20 text-blue-300 border border-blue-500/30
                @elseif($event['type'] === 'quiz') bg-amber-500/20 text-amber-300 border border-amber-500/30
                @else bg-coral-500/20 text-coral-300 border border-coral-500/30 @endif flex-shrink-0">
                {{ $event['label'] }}
            </span>
        </div>
        <div class="mt-2">
            <span class="text-[11px] font-mono text-gray-500 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @if($event['type'] === 'live_session')
                    {{ \Carbon\Carbon::parse($event['date'])->format('g:i A') }}
                @elseif($event['type'] === 'quiz')
                    {{ __('Pending') }}
                @else
                    {{ __('Due:') }} {{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }}
                @endif
            </span>
        </div>
    </a>
    @empty
    <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center">
    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700/50 flex items-center justify-center">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <p class="text-sm font-mono text-gray-500 uppercase tracking-wider">{{ __('No upcoming events') }}</p>
</div>
    @endforelse
</div>
</div>
