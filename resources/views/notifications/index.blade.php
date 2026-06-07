<x-layouts.dashboard>
    <x-slot name="title">Notifications — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Notifications</h1>
        @if($notifications->where('is_read', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-sm text-brand-400 hover:text-brand-300 transition-colors font-medium">Mark All Read</button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notification)
        @php $isUnread = !$notification->is_read; @endphp
        <a href="{{ $notification->link ?? '#' }}"
           class="block bg-surface-800 border border-white/10 rounded-xl p-5 mb-2 hover:border-white/20 transition-colors {{ !$isUnread ? 'opacity-60' : '' }}">
            <div class="flex items-start gap-4">
                <div class="mt-0.5">
                    @if($isUnread)
                        <div class="w-2.5 h-2.5 rounded-full bg-brand-500"></div>
                    @else
                        <div class="w-2.5 h-2.5 rounded-full bg-surface-600"></div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium {{ $isUnread ? 'text-white' : 'text-gray-400' }}">{{ $notification->title ?? 'Notification' }}</span>
                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm {{ $isUnread ? 'text-gray-300' : 'text-gray-500' }}">{{ $notification->message ?? '' }}</p>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-gray-400 font-medium">All clear</p>
            <p class="text-gray-600 text-sm mt-1">You have no notifications.</p>
        </div>
    @endforelse

    {{ $notifications->links() }}
</x-layouts.dashboard>
