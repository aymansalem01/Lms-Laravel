@props(['report'])

@if($report)
    @php
        $similarity = (float) ($report->overall_similarity ?? 0);
        $aiProb = (float) ($report->ai_probability ?? 0);

        if ($similarity < 15) {
            $badgeClass = 'text-green-400';
            $bgClass = 'bg-green-500/10 border-green-500/20';
            $icon = 'check';
        } elseif ($similarity < 40) {
            $badgeClass = 'text-yellow-400';
            $bgClass = 'bg-yellow-500/10 border-yellow-500/20';
            $icon = 'warning';
        } else {
            $badgeClass = 'text-red-400';
            $bgClass = 'bg-red-500/10 border-red-500/20';
            $icon = 'alert';
        }
    @endphp

    <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            @if($icon === 'check')
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            @elseif($icon === 'warning')
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            @else
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
            {{ __('Plagiarism Report') }}
        </h3>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500">{{ __('Similarity Score') }}</span>
                    <span class="text-sm font-semibold {{ $badgeClass }}">{{ $similarity }}%</span>
                </div>
                <div class="w-full bg-surface-700 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $similarity < 15 ? 'bg-green-500' : ($similarity < 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ $similarity }}%"></div>
                </div>
            </div>
            @if($aiProb > 0)
                <div class="text-center px-3 py-2 bg-surface-700 rounded-xl">
                    <p class="text-xs text-gray-500">{{ __('AI Probability') }}</p>
                    <p class="text-sm font-semibold {{ $aiProb > 60 ? 'text-red-400' : ($aiProb > 35 ? 'text-yellow-400' : 'text-green-400') }}">{{ $aiProb }}%</p>
                </div>
            @endif
        </div>
    </div>
@endif
