<x-layouts.dashboard>
    <x-slot name="title">{{ __('Attendance') }} — {{ $course->title }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Attendance') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('courses.show', $course) }}"
               class="text-sm text-brand-400 hover:text-brand-300 transition-colors">&larr; {{ __('Back to Course') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="attendanceApp()" x-init="init()" class="space-y-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <div class="flex items-end gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ __('Select Date') }}</label>
                    <input type="date" x-model="selectedDate"
                           class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">{{ __('Total') }}: <span class="text-white font-medium" x-text="sortedStudents.length"></span></span>
                    <span class="text-xs text-green-400">{{ __('Present') }}: <span x-text="presentCount"></span></span>
                    <span class="text-xs text-red-400">{{ __('Absent') }}: <span x-text="absentCount"></span></span>
                    <span class="text-xs text-yellow-400">{{ __('Late') }}: <span x-text="lateCount"></span></span>
                    <span class="text-xs text-blue-400">{{ __('Excused') }}: <span x-text="excusedCount"></span></span>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-surface-700/50">
                            <th class="text-left px-5 py-3 text-gray-400 font-medium cursor-pointer hover:text-white select-none" @click="sortBy = 'name'; sortAsc = !sortAsc">
                                <span class="flex items-center gap-1">
                                    {{ __('Student') }}
                                    <template x-if="sortBy === 'name'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </template>
                                </span>
                            </th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Present') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Absent') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Late') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Excused') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(student, index) in sortedStudents" :key="student.id">
                            <tr class="border-b border-white/5 hover:bg-surface-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-xs font-bold text-brand-300">
                                            <span x-text="student.initials"></span>
                                        </div>
                                        <span class="text-white font-medium" x-text="student.name"></span>
                                    </div>
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="present"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-green-500 bg-surface-700 border-white/10 focus:ring-green-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="absent"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-red-500 bg-surface-700 border-white/10 focus:ring-red-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="late"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-yellow-500 bg-surface-700 border-white/10 focus:ring-yellow-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="excused"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-blue-500 bg-surface-700 border-white/10 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-5 py-4 flex items-center justify-between">
                <button @click="clearAll" class="text-sm text-gray-500 hover:text-white transition-colors">{{ __('Clear All') }}</button>
                <button @click="saveAll"
                        class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-8 py-2.5 text-sm font-medium transition-colors"
                        :disabled="!selectedDate">
                    {{ __('Save All') }}
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('courses.attendance.bulk', $course) }}" id="attendance-form" class="hidden">
            @csrf
            <input type="hidden" name="date">
        </form>
    </div>

    @push('scripts')
    <script>
        function attendanceApp() {
            return {
                students: {!! json_encode($students->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'initials' => strtoupper(substr($s->name, 0, 1))])) !!},
                records: {!! json_encode($records) !!},
                selectedDate: new Date().toISOString().split('T')[0],
                attendance: {},
                sortBy: 'name',
                sortAsc: true,

                init() {
                    this.loadAttendance();
                },

                get sortedStudents() {
                    let sorted = [...this.students];
                    if (this.sortBy === 'name') {
                        sorted.sort((a, b) => a.name.localeCompare(b.name));
                    }
                    return this.sortAsc ? sorted : sorted.reverse();
                },

                loadAttendance() {
                    this.attendance = {};
                    let dateRecords = this.records[this.selectedDate] || {};
                    this.students.forEach(s => {
                        if (dateRecords[s.id]) {
                            this.attendance[s.id] = dateRecords[s.id];
                        }
                    });
                },

                saveAll() {
                    let form = document.getElementById('attendance-form');
                    form.querySelectorAll('.attendance-dynamic').forEach(el => el.remove());
                    form.querySelector('[name="date"]').value = this.selectedDate;
                    this.students.forEach(s => {
                        if (this.attendance[s.id]) {
                            let sInput = document.createElement('input');
                            sInput.type = 'hidden';
                            sInput.name = 'attendance[' + s.id + '][student_id]';
                            sInput.value = s.id;
                            sInput.classList.add('attendance-dynamic');
                            form.appendChild(sInput);
                            let stInput = document.createElement('input');
                            stInput.type = 'hidden';
                            stInput.name = 'attendance[' + s.id + '][status]';
                            stInput.value = this.attendance[s.id];
                            stInput.classList.add('attendance-dynamic');
                            form.appendChild(stInput);
                        }
                    });
                    form.submit();
                },

                clearAll() {
                    this.students.forEach(s => {
                        delete this.attendance[s.id];
                    });
                },

                get presentCount() {
                    return Object.values(this.attendance).filter(v => v === 'present').length;
                },

                get absentCount() {
                    return Object.values(this.attendance).filter(v => v === 'absent').length;
                },

                get lateCount() {
                    return Object.values(this.attendance).filter(v => v === 'late').length;
                },

                get excusedCount() {
                    return Object.values(this.attendance).filter(v => v === 'excused').length;
                },
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>