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
                            <td class="px-4 py-3 text-center text-gray-400">{{ $module->order_index ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $module->lessons_count ?? $module->lessons->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.modules.show', $module) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module? This will also delete all lessons within it.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No modules found.</td>
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
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-white mb-4">Create Module</h3>
            <form method="POST" action="{{ route('admin.modules.store') }}">
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
