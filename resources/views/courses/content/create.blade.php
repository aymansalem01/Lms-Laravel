<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Module') }} - {{ $course->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Content') }}</a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ __('Create Module') }}</h1>
        <form method="POST" action="{{ route('courses.content.store', $course) }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Module Title') }}</label>
                <input type="text" name="title" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Order Index') }}</label>
                <input type="number" name="order_index" min="0"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
                <p class="text-xs text-gray-500 mt-1">{{ __('Leave blank to append at the end.') }}</p>
            </div>
            <div class="flex items-center gap-3 justify-end pt-2">
                <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white px-4 py-2 transition-colors">{{ __('Cancel') }}</a>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-6 py-2 text-sm font-medium transition-colors">{{ __('Create Module') }}</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
