<x-layouts.dashboard>
    <x-slot name="title">{{ isset($item) ? 'Edit' : 'Add' }} Portfolio Item — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('portfolio.show', auth()->user()) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to portfolio
        </a>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-xl p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-white mb-6">{{ isset($item) ? 'Edit Item' : 'Add Portfolio Item' }}</h1>

        <form method="POST" action="{{ route('portfolio.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $item->title ?? '') }}" placeholder="e.g. Final Film Project" class="input-dashboard">
                @error('title')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe your work..." class="input-dashboard resize-none">{{ old('description', $item->description ?? '') }}</textarea>
                @error('description')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="media_type" class="block text-sm font-medium text-gray-300 mb-1.5">Media Type</label>
                <select id="media_type" name="media_type" class="input-dashboard">
                    <option value="video" {{ old('media_type', $item->media_type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="audio" {{ old('media_type', $item->media_type ?? '') === 'audio' ? 'selected' : '' }}>Audio</option>
                    <option value="image" {{ old('media_type', $item->media_type ?? '') === 'image' ? 'selected' : '' }}>Image</option>
                    <option value="document" {{ old('media_type', $item->media_type ?? '') === 'document' ? 'selected' : '' }}>Document</option>
                    <option value="link" {{ old('media_type', $item->media_type ?? '') === 'link' ? 'selected' : '' }}>Link</option>
                </select>
                @error('media_type')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="media_url" class="block text-sm font-medium text-gray-300 mb-1.5">Media URL</label>
                <input id="media_url" name="media_url" type="url" value="{{ old('media_url', $item->media_url ?? '') }}" placeholder="https://youtube.com/watch?v=..." class="input-dashboard">
                @error('media_url')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="media_file" class="block text-sm font-medium text-gray-300 mb-1.5">Or Upload File</label>
                <input id="media_file" name="media_file" type="file"
                       accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mp3,.pdf,.doc,.docx"
                       class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 file:cursor-pointer file:transition-colors bg-surface-800 border border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-brand-500">
                @error('media_file')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $item->is_public ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 bg-surface-800 text-brand-500 focus:ring-brand-500">
                    <span class="text-sm text-gray-300">Make this item public</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('portfolio.show', auth()->user()) }}" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</a>
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ isset($item) ? 'Update' : 'Add Item' }}</button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
