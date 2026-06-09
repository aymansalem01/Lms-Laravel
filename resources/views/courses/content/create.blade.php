<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Module') }} - {{ $course->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.content.index', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to Content') }}</a>
    </div>

    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ __('Create Module') }}</h1>
        <form method="POST" action="{{ route('courses.content.store', $course) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Module Title') }}</label>
                <input type="text" name="title" required
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Description') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <textarea name="description" rows="3"
                          class="w-full bg-surface-700 border border-surface-600 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-colors resize-none"
                          placeholder="{{ __('Brief description of this module...') }}"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Attachment') }} <span class="text-gray-500">({{ __('optional') }})</span></label>
                <label class="relative flex items-center gap-3 bg-surface-700 border border-dashed border-surface-600 rounded-lg px-4 py-3 cursor-pointer hover:border-brand-500/50 transition-colors">
                    <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <span class="text-sm text-gray-400">{{ __('Upload a file (PDF, DOC, ZIP, images, etc.)') }}</span>
                    <input type="file" name="module_file"
                           @change="const f = $event.target.files[0]; if (f) { $el.closest('label').querySelector('span').textContent = f.name; }"
                           class="hidden">
                </label>
                @error('module_file') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
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
