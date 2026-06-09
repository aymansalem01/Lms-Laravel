<x-layouts.dashboard>
    <x-slot name="title">{{ __('messages.user_management') }} — SAE LMS</x-slot>

    <div x-data="{
            addOpen: false, inviteOpen: false,
            search: '{{ request('search') }}',
            role: '{{ request('role') }}',
            program: '{{ request('program') }}',
            searchTimer: null,
            loading: false,
            async submit() {
                const params = new URLSearchParams();
                if (this.search) params.set('search', this.search);
                if (this.role) params.set('role', this.role);
                if (this.program) params.set('program', this.program);
                this.loading = true;
                try {
                    const res = await fetch('{{ route('admin.users.index') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await res.text();
                    this.$refs.tableWrapper.innerHTML = html;
                } finally {
                    this.loading = false;
                }
            },
            clearFilters() {
                this.search = '';
                this.role = '';
                this.program = '';
                this.submit();
            }
         }"
         x-effect="addOpen && $nextTick(() => $refs.addName.focus()); inviteOpen && $nextTick(() => $refs.inviteEmail.focus())"
         class="contents">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('messages.users') }}</h1>
        <div class="flex items-center gap-2">
            <button @click="addOpen = true" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                {{ __('messages.add_user') }}
            </button>
            <button @click="inviteOpen = true" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.invite_user') }}
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        {{-- Search --}}
        <div class="relative flex-1 w-full">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="search" @input="clearTimeout(searchTimer); searchTimer = setTimeout(() => submit(), 500)" @blur="clearTimeout(searchTimer); submit()" placeholder="{{ __('messages.search_users') }}" class="w-full bg-surface-800 border border-white/10 text-white placeholder-gray-600 rounded-xl py-3 pl-12 pr-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors" autocomplete="off">
        </div>

        {{-- Role filter --}}
        <select x-model="role" @change="$nextTick(() => submit())" class="flex-1 w-full bg-surface-800 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">{{ __('All Roles') }}</option>
            <option value="student">{{ __('messages.role_student') }}</option>
            <option value="instructor">{{ __('messages.role_instructor') }}</option>
            <option value="admin">{{ __('messages.role_admin') }}</option>
        </select>

        {{-- Program filter --}}
        <select x-model="program" @change="$nextTick(() => submit())" class="flex-1 w-full bg-surface-800 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">{{ __('All Programs') }}</option>
            @foreach($programs ?? [] as $prog)
                <option value="{{ $prog }}">{{ $prog }}</option>
            @endforeach
        </select>

        {{-- Clear filters --}}
        <button x-show="search || role || program" @click="clearFilters()" class="text-sm text-gray-400 hover:text-white transition-colors whitespace-nowrap px-3 py-2">
            {{ __('Clear Filters') }}
        </button>
    </div>

    {{-- Loading indicator --}}
    <div x-show="loading" class="flex items-center justify-center py-4 mb-4">
        <svg class="animate-spin w-6 h-6 text-brand-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    {{-- Table --}}
    <div x-ref="tableWrapper">
        @include('admin.users._table')
    </div>

    {{-- Add User Modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="addOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl" @click.away="addOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.add_user') }}</h3>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.full_name') }}</label>
                        <input id="add_name" x-ref="addName" name="name" type="text" placeholder="{{ __('messages.full_name') }}" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                    <div>
                        <label for="add_email" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.email_address') }}</label>
                        <input id="add_email" name="email" type="email" placeholder="user@example.com" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="add_password" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.password') }}</label>
                        <input id="add_password" name="password" type="password" minlength="8" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                    <div>
                        <label for="add_role" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.role') }}</label>
                        <select id="add_role" name="role" class="w-full bg-surface-700 border border-white/20 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
                            <option value="student">{{ __('messages.role_student') }}</option>
                            <option value="instructor">{{ __('messages.role_instructor') }}</option>
                            <option value="admin">{{ __('messages.role_admin') }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="add_program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.program') }}</label>
                    <input id="add_program" name="program" type="text" placeholder="e.g. Film Production" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>
                <div class="mb-4">
                    <label for="add_bio" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.bio') }}</label>
                    <textarea id="add_bio" name="bio" rows="2" placeholder="{{ __('messages.bio') }}" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="addOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.create_user') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Invite User Modal --}}
    <div x-show="inviteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="inviteOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl" @click.away="inviteOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.invite_user') }}</h3>
            <form method="POST" action="{{ route('admin.users.invite') }}">
                @csrf
                <div class="mb-4">
                    <label for="invite_email" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.email_address') }}</label>
                    <input id="invite_email" x-ref="inviteEmail" name="email" type="email" placeholder="user@example.com" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="invite_role" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.role') }}</label>
                    <select id="invite_role" name="role" class="input-dashboard">
                        <option value="student">{{ __('messages.role_student') }}</option>
                        <option value="instructor">{{ __('messages.role_instructor') }}</option>
                        <option value="admin">{{ __('messages.role_admin') }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="inviteOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.send_invite') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-layouts.dashboard>
