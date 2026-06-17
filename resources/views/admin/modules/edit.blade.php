<x-layouts.dashboard>
    <x-slot name="title">Edit Module — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.modules.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to modules
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">Edit Module</h2>

            <form method="POST" action="{{ route('admin.modules.update', $module) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $module->title) }}" class="input-dashboard">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Description <span class="text-gray-500">(optional)</span></label>
                    <textarea id="description" name="description" rows="3" class="input-dashboard resize-none">{{ old('description', $module->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Attachment <span class="text-gray-500">(optional)</span></label>
                    <label class="flex items-center gap-2 bg-surface-700 border border-dashed border-surface-600 rounded-lg px-3 py-2.5 cursor-pointer hover:border-brand-500/50 transition-colors">
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="text-sm text-gray-500">{{ $module->file_path ? basename($module->file_path) : 'Upload a file (PDF, DOC, ZIP, etc.)' }}</span>
                        <input type="file" name="module_file"
                               @change="const f = $event.target.files[0]; if (f) { $el.closest('label').querySelector('span').textContent = f.name; }"
                               class="hidden">
                    </label>
                    @if($module->file_path)
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-gray-500">Current:</span>
                            <a href="{{ Storage::url($module->file_path) }}" target="_blank" class="text-xs text-brand-400 hover:text-brand-300">{{ basename($module->file_path) }}</a>
                            <label class="inline-flex items-center gap-1 text-xs text-red-400 hover:text-red-300 cursor-pointer">
                                <input type="checkbox" name="remove_module_file" value="1" class="rounded border-surface-600 bg-surface-700 text-red-500 focus:ring-red-500/30">
                                Remove
                            </label>
                        </div>
                    @endif
                    @error('module_file') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="order_index" class="block text-sm font-medium text-gray-300 mb-1.5">Order Index</label>
                    <input id="order_index" name="order_index" type="number" value="{{ old('order_index', $module->order_index) }}" class="input-dashboard">
                    @error('order_index') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.modules.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</a>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
