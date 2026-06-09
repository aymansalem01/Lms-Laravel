<x-layouts.dashboard>
    <x-slot name="title">Module Management — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Modules</h1>
        <button @click="$dispatch('open-create-module')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Module
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Instructor</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">File</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Order</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Lessons</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($modules as $module)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.modules.show', $module) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $module->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $module->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $module->course->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($module->file_path)
                                    <a href="{{ Storage::url($module->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-brand-400 hover:text-brand-300 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ basename($module->file_path) }}
                                    </a>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $module->order_index ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $module->lessons_count ?? $module->lessons->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.modules.show', $module) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <a href="{{ route('admin.modules.edit', $module) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module? This will also delete all lessons within it.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No modules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $modules->links() }}

    {{-- Create Module Modal --}}
    <div x-data="{ open: false }" @open-create-module.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold text-white mb-4">Create Module</h3>
            <form method="POST" action="{{ route('admin.modules.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="module_course_id" class="block text-sm font-medium text-gray-300 mb-1.5">Course</label>
                    <select id="module_course_id" name="course_id" class="input-dashboard">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="module_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="module_title" name="title" type="text" placeholder="Module title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="module_description" class="block text-sm font-medium text-gray-300 mb-1.5">Description <span class="text-gray-500">(optional)</span></label>
                    <textarea id="module_description" name="description" rows="2" placeholder="Brief description..." class="input-dashboard resize-none"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Attachment <span class="text-gray-500">(optional)</span></label>
                    <label class="flex items-center gap-2 bg-surface-700 border border-dashed border-surface-600 rounded-lg px-3 py-2.5 cursor-pointer hover:border-brand-500/50 transition-colors">
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="text-sm text-gray-500">Upload a file (PDF, DOC, ZIP, etc.)</span>
                        <input type="file" name="module_file"
                               @change="const f = $event.target.files[0]; if (f) { $el.closest('label').querySelector('span').textContent = f.name; }"
                               class="hidden">
                    </label>
                </div>
                <div class="mb-4">
                    <label for="module_order_index" class="block text-sm font-medium text-gray-300 mb-1.5">Order Index</label>
                    <input id="module_order_index" name="order_index" type="number" placeholder="0" class="input-dashboard">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
