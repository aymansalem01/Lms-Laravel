<x-layouts.dashboard>
    <x-slot name="title">{{ $user->name }}'s Portfolio — SAE LMS</x-slot>

    {{-- Profile Hero --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl p-8 mb-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-600/10 via-transparent to-coral-500/10 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-brand-500 to-coral-500 p-[3px]">
                    <div class="w-full h-full rounded-full bg-surface-800 flex items-center justify-center overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-center sm:text-left flex-1">
                <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                @if($user->program)
                    <p class="text-brand-300 text-sm font-medium mt-1">{{ $user->program }}</p>
                @endif
                @if($user->bio)
                    <p class="text-gray-400 text-sm mt-3 max-w-lg">{{ $user->bio }}</p>
                @endif
            </div>
            @if(auth()->id() === $user->id)
                <a href="{{ route('portfolio.edit') }}" class="inline-flex items-center gap-2 bg-surface-600 hover:bg-surface-700 text-white rounded-xl px-5 py-2.5 text-sm font-medium transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Add Item
                </a>
            @endif
        </div>
    </div>

    {{-- Portfolio Items Grid --}}
    @php
        $items = $portfolioItems->filter(fn($item) => $item->is_public || auth()->id() === $user->id || auth()->user()?->role === 'admin');
    @endphp

    @if($items->isEmpty())
        <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p class="text-gray-400 font-medium">No portfolio items yet</p>
            <p class="text-gray-600 text-sm mt-1">{{ $user->name }} hasn't added any portfolio items.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden group">
                    {{-- Media Preview --}}
                    <div class="aspect-video bg-surface-700 relative overflow-hidden">
                        @if($item->media_type === 'video' && $item->media_url)
                            <div class="w-full h-full bg-surface-700 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ $item->media_url }}" target="_blank" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors">Watch</a>
                            </div>
                        @elseif($item->media_type === 'audio' && $item->media_url)
                            <div class="w-full h-full flex items-center justify-center bg-surface-700">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            </div>
                        @elseif($item->media_type === 'image' && $item->media_url)
                            <img src="{{ $item->media_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-surface-700">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        @if(!$item->is_public)
                            <span class="absolute top-2 right-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">Private</span>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="text-white font-semibold text-sm">{{ $item->title }}</h3>
                        @if($item->description)
                            <p class="text-gray-400 text-xs mt-1 line-clamp-2">{{ $item->description }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.dashboard>
