<x-layouts.dashboard>
    <x-slot name="title">{{ $announcement->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to announcements
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $announcement->title }}</h2>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($announcement->priority === 'urgent') bg-red-500/20 text-red-400
                                @elseif($announcement->priority === 'high') bg-amber-500/20 text-amber-400
                                @elseif($announcement->priority === 'low') bg-blue-500/20 text-blue-400
                                @else bg-purple-500/20 text-purple-400
                                @endif">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="prose prose-invert max-w-none text-gray-300 text-sm leading-relaxed">
                    {{ $announcement->content }}
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 text-xs text-gray-500">
                    <p>Updated: {{ $announcement->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Author --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Author</h4>
                <p class="text-white font-medium">{{ $announcement->author->name ?? 'Unknown' }}</p>
                <p class="text-gray-500 text-xs">{{ $announcement->author->email ?? '' }}</p>
            </div>

            {{-- Course --}}
            @if($announcement->course)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                    <a href="{{ route('admin.courses.show', $announcement->course) }}" class="text-brand-400 hover:text-brand-300 text-sm">{{ $announcement->course->title }}</a>
                </div>
            @endif

            {{-- Dismissals --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Dismissals ({{ $announcement->dismissals->count() }})</h4>
                @forelse($announcement->dismissals as $dismissal)
                    <div class="flex items-center justify-between py-1.5 border-b border-white/5 last:border-0">
                        <span class="text-sm text-gray-300">{{ $dismissal->user->name ?? 'Unknown' }}</span>
                        <span class="text-xs text-gray-500">{{ $dismissal->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No dismissals yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.dashboard>
