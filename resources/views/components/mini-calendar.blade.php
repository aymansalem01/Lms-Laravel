@php
$user = auth()->user();

if ($user->role === 'admin') {
    $courseIds = \App\Models\Course::pluck('id');
} elseif ($user->role === 'instructor') {
    $taughtIds = $user->taughtCourses()->pluck('id');
    $coIds = $user->coInstructedCourses()->pluck('courses.id');
    $courseIds = $taughtIds->merge($coIds)->unique();
} else {
    $courseIds = $user->enrolledCourses()->pluck('courses.id');
}

$assignments = \App\Models\Assignment::whereIn('course_id', $courseIds)
    ->whereNotNull('due_date')
    ->get(['id', 'title', 'due_date', 'course_id']);

$liveSessions = \App\Models\LiveSession::whereIn('course_id', $courseIds)
    ->whereNotNull('scheduled_at')
    ->get(['id', 'title', 'scheduled_at', 'course_id']);

$eventMap = [];
$month = now()->month;
$year = now()->year;

foreach ($assignments as $a) {
    $d = \Carbon\Carbon::parse($a->due_date);
    $key = $d->format('Y-m-d');
    $eventMap[$key][] = ['title' => $a->title, 'type' => 'Assignment', 'dot' => 'bg-coral-500'];
}
foreach ($liveSessions as $ls) {
    $d = \Carbon\Carbon::parse($ls->scheduled_at);
    $key = $d->format('Y-m-d');
    $eventMap[$key][] = ['title' => $ls->title, 'type' => 'Live', 'dot' => 'bg-brand-500'];
}
@endphp

<div class="bg-surface-800 border border-white/5 rounded-xl p-4">
    <h3 class="section-label mb-3 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        {{ __('Upcoming') }}
    </h3>

    @if(empty($eventMap))
        <p class="text-xs text-gray-500 text-center py-4 font-mono">{{ __('No upcoming events') }}</p>
    @else
        @php $shown = 0; @endphp
        @foreach($eventMap as $dateKey => $events)
            @if($shown >= 5) @break @endif
            @php
                $parsed = \Carbon\Carbon::parse($dateKey);
                $shown++;
            @endphp
            <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-700 transition-colors group mb-1">
                <div class="text-center shrink-0 w-10 pt-0.5">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase leading-tight font-mono">{{ $parsed->format('M') }}</p>
                    <p class="text-sm font-bold text-white leading-tight">{{ $parsed->format('j') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-200 group-hover:text-white transition-colors truncate">{{ $events[0]['title'] }}</p>
                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded mt-0.5 {{ $events[0]['type'] === 'Assignment' ? 'bg-coral-500/20 text-coral-400' : 'bg-brand-500/20 text-brand-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $events[0]['dot'] }} inline-block"></span>
                        {{ __($events[0]['type']) }}
                    </span>
                </div>
            </div>
        @endforeach
    @endif
</div>