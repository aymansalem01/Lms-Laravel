<x-layouts.dashboard>
    <x-slot name="title">{{ $module->title }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.modules.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to modules
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $module->title }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $module->course->title ?? '—' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.modules.edit', $module) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module and all its lessons?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-white mb-3">Lessons ({{ $module->lessons->count() }})</h3>
                @forelse($module->lessons->sortBy('order_index') as $lesson)
                    <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-6">{{ $lesson->order_index ?? '—' }}</span>
                            <a href="{{ route('admin.lessons.show', $lesson) }}" class="text-sm text-white hover:text-brand-300 transition-colors">{{ $lesson->title }}</a>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.lessons.show', $lesson) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                            <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No lessons in this module.</p>
                @endforelse

                <div class="mt-4 pt-4 border-t border-white/10">
                    <a href="{{ route('admin.lessons.create', ['module_id' => $module->id]) }}" class="inline-flex items-center gap-1 text-sm text-brand-400 hover:text-brand-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Lesson
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $module->course->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $module->course->instructor->name ?? '—' }}</p>
            </div>
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Order Index</h4>
                <p class="text-white text-sm">{{ $module->order_index ?? '—' }}</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
