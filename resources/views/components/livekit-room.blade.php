<div x-data="livekitRoom()" x-init="init()" class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
    <div class="p-4 border-b border-white/10 flex items-center justify-between">
        <span class="text-sm font-medium text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full" :class="connected ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500'"></span>
            <span x-text="connected ? 'Connected' : 'Connecting...'"></span>
        </span>
        <button x-show="connected" @click="disconnect()" class="text-xs text-red-400 hover:text-red-300 transition-colors px-3 py-1.5 rounded-lg border border-red-500/30 hover:border-red-500/50">
            Leave Room
        </button>
    </div>

    <div x-show="!connected && !error" class="flex items-center justify-center h-64">
        <div class="flex flex-col items-center gap-3">
            <svg class="w-8 h-8 text-brand-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-gray-400 text-sm">Connecting to room...</p>
        </div>
    </div>

    <div x-show="error" class="flex items-center justify-center h-64">
        <div class="text-center">
            <p class="text-red-400 text-sm mb-2" x-text="error"></p>
            <button @click="init()" class="text-brand-400 hover:text-brand-300 text-xs underline">Retry</button>
        </div>
    </div>

    <div x-show="connected" class="relative bg-black/40" style="min-height: 400px;">
        <div id="livekit-stage" class="relative w-full" style="height: 500px;"></div>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-black/60 rounded-xl px-4 py-2.5">
            <button @click="toggleMic()" class="p-2 rounded-lg transition-colors" :class="micEnabled ? 'bg-surface-600 hover:bg-surface-500 text-white' : 'bg-red-500/30 text-red-400 hover:bg-red-500/50'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
            </button>
            <button @click="toggleCam()" class="p-2 rounded-lg transition-colors" :class="camEnabled ? 'bg-surface-600 hover:bg-surface-500 text-white' : 'bg-red-500/30 text-red-400 hover:bg-red-500/50'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/livekit-client/dist/livekit-client.umd.min.js"></script>
<script>
function livekitRoom() {
    return {
        connected: false,
        error: null,
        micEnabled: true,
        camEnabled: true,
        room: null,
        roomName: '{{ $roomName }}',
        sessionId: {{ $sessionId ?? 'null' }},

        async init() {
            this.error = null;
            try {
                const res = await fetch('{{ route("api.livekit.token") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ roomName: this.roomName, sessionId: this.sessionId }),
                });
                const data = await res.json();
                if (!data.token) throw new Error(data.error || 'Failed to get token');

                const host = data.apiHost || '{{ $host }}';
                const room = new LivekitClient.Room({
                    adaptiveStream: true,
                    dynacast: true,
                });

                room.on(LivekitClient.RoomEvent.ParticipantConnected, () => this.renderParticipants(room));
                room.on(LivekitClient.RoomEvent.ParticipantDisconnected, () => this.renderParticipants(room));
                room.on(LivekitClient.RoomEvent.TrackSubscribed, (track, publication, participant) => {
                    this.attachTrack(track, participant);
                });
                room.on(LivekitClient.RoomEvent.TrackUnsubscribed, (track) => {
                    track.detach();
                });

                await room.connect(host, data.token);
                await room.localParticipant.enableCameraAndMicrophone();

                this.room = room;
                this.connected = true;
                this.renderParticipants(room);
            } catch (e) {
                this.error = e.message || 'Connection failed';
            }
        },

        renderParticipants(room) {
            const stage = document.getElementById('livekit-stage');
            if (!stage) return;
            const existing = stage.querySelector('.participants-grid');
            if (existing) existing.remove();

            const grid = document.createElement('div');
            grid.className = 'participants-grid absolute inset-0 grid gap-2 p-4';
            const participants = [room.localParticipant, ...Array.from(room.remoteParticipants.values())];
            const cols = Math.min(participants.length, 3);
            grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

            participants.forEach(p => {
                const el = document.createElement('div');
                el.className = 'relative rounded-xl overflow-hidden bg-surface-900 flex items-center justify-center';
                el.dataset.participant = p.sid;

                const label = document.createElement('div');
                label.className = 'absolute bottom-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded-md';
                label.textContent = p.name || p.identity || 'Anonymous';
                el.appendChild(label);

                grid.appendChild(el);
            });

            stage.appendChild(grid);
        },

        attachTrack(track, participant) {
            const stage = document.getElementById('livekit-stage');
            if (!stage) return;
            const el = stage.querySelector(`[data-participant="${participant.sid}"]`);
            if (!el) return;
            const existing = el.querySelector('video, audio');
            if (existing) existing.remove();

            const mediaEl = track.attach();
            mediaEl.className = 'absolute inset-0 w-full h-full object-cover';
            if (track.kind === 'video') {
                mediaEl.style.objectFit = 'cover';
            }
            el.appendChild(mediaEl);
        },

        disconnect() {
            if (this.room) { this.room.disconnect(); this.room = null; }
            this.connected = false;
            const stage = document.getElementById('livekit-stage');
            const grid = stage?.querySelector('.participants-grid');
            if (grid) grid.remove();
        },

        toggleMic() {
            this.micEnabled = !this.micEnabled;
            this.room?.localParticipant.setMicrophoneEnabled(this.micEnabled);
        },

        toggleCam() {
            this.camEnabled = !this.camEnabled;
            this.room?.localParticipant.setCameraEnabled(this.camEnabled);
        },
    };
}
</script>
@endpush
