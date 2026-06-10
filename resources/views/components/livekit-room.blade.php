@php $isGuest = !auth()->check(); @endphp
<div x-data="livekitRoom()" x-init="init()" class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
    <div class="p-4 border-b border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-2 h-2 rounded-full" :class="connected ? 'bg-emerald-400 animate-pulse' : 'bg-gray-500'"></span>
            <span class="text-sm font-medium text-white" x-text="connected ? (pinnedSid ? 'Focus view' : 'Connected') : 'Connecting...'"></span>
            <span x-show="connected" class="text-xs px-2 py-0.5 rounded-full"
                  :class="qualityClass" x-text="connectionQuality"></span>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="connected && pinnedSid" @click="pinnedSid = null" class="text-xs text-gray-400 hover:text-white transition-colors px-2 py-1 rounded-lg border border-white/10 hover:border-white/30">Gallery</button>
            <button x-show="connected" @click="disconnect()" class="text-xs text-red-400 hover:text-red-300 transition-colors px-3 py-1.5 rounded-lg border border-red-500/30 hover:border-red-500/50">Leave</button>
        </div>
    </div>

    <div x-show="!connected && !error" class="flex items-center justify-center" style="height:400px">
        <div class="flex flex-col items-center gap-3">
            <svg class="w-8 h-8 text-brand-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-gray-400 text-sm">Connecting to room...</p>
        </div>
    </div>

    <div x-show="error" class="flex items-center justify-center" style="height:400px">
        <div class="text-center">
            <p class="text-red-400 text-sm mb-2" x-text="error"></p>
            <button @click="init()" class="text-brand-400 hover:text-brand-300 text-xs underline">Retry</button>
        </div>
    </div>

    <div x-show="connected" class="relative" style="min-height:400px">
        <div id="livekit-container" class="relative bg-black/40" style="min-height:400px">
            <div id="livekit-stage" class="relative w-full" style="height:500px">
                <div x-show="fullscreen" class="absolute top-4 left-4 z-20">
                    <button @click="toggleFullscreen()" class="bg-black/70 hover:bg-black/90 text-white text-xs px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Exit Fullscreen
                    </button>
                </div>
            </div>
            <style>#livekit-stage:fullscreen{width:100vw;height:100vh;background:#000}#livekit-stage:fullscreen .participants-grid{width:100%;height:100%}</style>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-black/70 rounded-xl px-3 py-2">
                <button @click="toggleMic()" title="Microphone" class="p-2 rounded-lg transition-colors" :class="micEnabled ? 'bg-surface-600 hover:bg-surface-500 text-white' : 'bg-red-500/30 text-red-400'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                </button>
                <button @click="toggleCam()" title="Camera" class="p-2 rounded-lg transition-colors" :class="camEnabled ? 'bg-surface-600 hover:bg-surface-500 text-white' : 'bg-red-500/30 text-red-400'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
                <div class="w-px h-6 bg-white/10"></div>
                <button @click="toggleScreenShare()" title="Share screen" class="p-2 rounded-lg transition-colors" :class="sharingScreen ? 'bg-brand-500/30 text-brand-400' : 'bg-surface-600 hover:bg-surface-500 text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </button>
                <button @click="setBackgroundBlur()" title="Background blur" class="p-2 rounded-lg transition-colors" :class="bgBlur > 0 ? 'bg-brand-500/30 text-brand-400' : 'bg-surface-600 hover:bg-surface-500 text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                <div class="w-px h-6 bg-white/10"></div>
                <button @click="toggleStats()" title="Connection stats" class="p-2 rounded-lg transition-colors" :class="showStats ? 'bg-brand-500/30 text-brand-400' : 'bg-surface-600 hover:bg-surface-500 text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </button>
                <button @click="showChat = !showChat" title="Chat" class="p-2 rounded-lg transition-colors" :class="showChat ? 'bg-brand-500/30 text-brand-400' : 'bg-surface-600 hover:bg-surface-500 text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </button>
                <div class="w-px h-6 bg-white/10"></div>
                <button @click="cycleQuality()" title="Video quality" class="p-2 rounded-lg bg-surface-600 hover:bg-surface-500 text-white">
                    <span class="text-[10px] font-semibold px-0.5" x-text="videoQuality"></span>
                </button>
                <button @click="toggleFullscreen()" title="Fullscreen" class="p-2 rounded-lg bg-surface-600 hover:bg-surface-500 text-white">
                    <svg x-show="!fullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                    <svg x-show="fullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="showChat" class="absolute top-0 right-0 w-80 h-full bg-surface-900 border-l border-white/10 flex flex-col z-10">
            <div class="p-3 border-b border-white/10 flex items-center justify-between">
                <span class="text-sm font-medium text-white">Chat</span>
                <button @click="showChat = false" class="text-gray-400 hover:text-white p-1">&times;</button>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2" x-ref="chatBox">
                <template x-for="(msg, i) in chatMessages" :key="i">
                    <div class="text-sm" :class="msg.isLocal ? 'text-right' : 'text-left'">
                        <span class="text-xs text-gray-500" x-text="msg.sender"></span>
                        <p class="inline-block rounded-lg px-3 py-1.5 mt-0.5 max-w-[85%] break-words"
                           :class="msg.isLocal ? 'bg-brand-600 text-white' : 'bg-surface-700 text-gray-200'"
                           x-text="msg.text"></p>
                    </div>
                </template>
            </div>
            <form @submit.prevent="sendMessage()" class="p-3 border-t border-white/10 flex gap-2">
                <input x-model="chatInput" type="text" placeholder="Type a message..." class="flex-1 bg-surface-800 text-white text-sm rounded-lg px-3 py-2 border border-white/10 focus:border-brand-500 outline-none">
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white px-3 py-2 rounded-lg text-sm">Send</button>
            </form>
        </div>

        <div x-show="showStats" class="absolute top-3 left-3 bg-black/80 rounded-xl p-4 text-xs text-gray-300 space-y-1.5 z-10 min-w-[180px]">
            <div class="flex items-center gap-2"><span class="text-gray-500">Status:</span><span x-text="stats.status"></span></div>
            <div class="flex items-center gap-2"><span class="text-gray-500">Quality:</span><span x-text="connectionQuality" :class="qualityClass"></span></div>
            <div class="flex items-center gap-2"><span class="text-gray-500">Participants:</span><span x-text="stats.participants"></span></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function livekitRoom() {
    return {
        connected: false,
        error: null,
        micEnabled: true,
        camEnabled: true,
        fullscreen: false,
        sharingScreen: false,
        pinnedSid: null,
        showChat: false,
        chatMessages: [],
        chatInput: '',
        showStats: false,
        connectionQuality: 'unknown',
        bgBlur: 0,
        videoQuality: 'Auto',
        room: null,
        roomName: '{{ $roomName }}',
        sessionId: {{ $sessionId ?? 'null' }},
        isGuest: {{ $isGuest ? 'true' : 'false' }},

        get qualityClass() {
            return {
                'unknown': 'bg-gray-500/20 text-gray-400',
                'excellent': 'bg-emerald-500/20 text-emerald-400',
                'good': 'bg-blue-500/20 text-blue-400',
                'poor': 'bg-amber-500/20 text-amber-400',
            }[this.connectionQuality] || 'bg-gray-500/20 text-gray-400';
        },

        get stats() {
            const rp = this.room?.remoteParticipants;
            const count = rp && typeof rp.forEach === 'function' ? (() => { let n = 0; rp.forEach(() => n++); return n; })() : 0;
            return {
                status: this.room?.state || 'disconnected',
                participants: count + (this.room?.localParticipant ? 1 : 0),
            };
        },

        async init() {
            this.error = null;
            try {
                if (typeof window.loadLiveKit === 'function') await window.loadLiveKit();

                if (window.__livekitRoom && window.__livekitRoom.state === 'connected') {
                    this.room = window.__livekitRoom;
                    this.connected = true;
                    await this.$nextTick();
                    this.renderParticipants(this.room);
                    return;
                }

                let url, body;
                @if($isGuest)
                    url = '{{ route("api.livekit.public-token") }}';
                    body = JSON.stringify({ room: this.roomName });
                @else
                    url = '{{ route("api.livekit.token") }}';
                    body = JSON.stringify({ roomName: this.roomName, sessionId: this.sessionId });
                @endif

                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: body,
                });
                const data = await res.json();
                if (!data.token) throw new Error(data.error || 'Failed to get token');

                const host = data.apiHost || '{{ $host }}';
                const room = new LivekitClient.Room({
                    adaptiveStream: true,
                    dynacast: true,
                });

                room.on('participantConnected', () => this.renderParticipants(room));
                room.on('participantDisconnected', () => this.renderParticipants(room));
                room.on('trackSubscribed', (track, publication, participant) => {
                    this.attachTrack(track, participant);
                });
                room.on('trackUnsubscribed', (track) => { track.detach(); });
                room.on('localTrackPublished', (publication) => {
                    if (publication && publication.track) this.attachTrack(publication.track, room.localParticipant);
                });
                room.on('connectionQualityChanged', (quality, participant) => {
                    if (!participant || participant.sid === room.localParticipant?.sid) {
                        const q = quality?.toString?.()?.toLowerCase?.() || '';
                        if (['excellent','good','poor','unknown'].includes(q)) this.connectionQuality = q;
                    }
                });
                room.on('dataReceived', (payload, participant, kind, topic) => {
                    try {
                        const decoder = new TextDecoder();
                        const msg = JSON.parse(decoder.decode(payload));
                        const sender = participant?.name || participant?.identity || 'Anonymous';
                        this.chatMessages.push({ sender, text: msg.text, isLocal: false });
                        this.$nextTick(() => {
                            const box = this.$refs?.chatBox;
                            if (box) box.scrollTop = box.scrollHeight;
                        });
                    } catch (_) {}
                });

                await room.connect(host, data.token);

                this.room = room;
                this.connected = true;

                sessionStorage.setItem('livekit_session', JSON.stringify({ host, room: this.roomName }));
                window.__livekitRoom = room;

                await this.$nextTick();
                this.renderParticipants(room);

                await new Promise(r => setTimeout(r, 800));

                const result = await room.localParticipant.enableCameraAndMicrophone();
                if (result && Array.isArray(result)) {
                    result.forEach(t => { if (t) this.attachTrack(t, room.localParticipant); });
                }

                this.micEnabled = true;
                this.camEnabled = true;

                document.addEventListener('fullscreenchange', () => {
                    this.fullscreen = !!document.fullscreenElement;
                });
            } catch (e) {
                this.error = e?.message || e || 'Connection failed';
            }
        },

        renderParticipants(room) {
            const stage = document.getElementById('livekit-stage');
            if (!stage) return;
            let grid = stage.querySelector('.participants-grid');
            if (!grid) {
                grid = document.createElement('div');
                grid.className = 'participants-grid absolute inset-0 grid gap-2 p-4';
                stage.appendChild(grid);
            }

            let participants = [];
            if (room.localParticipant) participants.push(room.localParticipant);
            if (room.remoteParticipants && typeof room.remoteParticipants.forEach === 'function') {
                room.remoteParticipants.forEach(p => participants.push(p));
            }

            const pinned = participants.find(p => p.sid === this.pinnedSid);

            if (pinned) {
                participants = [pinned];
                grid.className = 'participants-grid absolute inset-0 flex items-center justify-center p-4';
                grid.style.gridTemplateColumns = '1fr';
                grid.style.display = 'flex';
            } else {
                grid.className = 'participants-grid absolute inset-0 grid gap-2 p-4';
                grid.style.display = 'grid';
                grid.style.gridTemplateColumns = `repeat(${Math.min(participants.length, 3)}, 1fr)`;
            }

            participants.forEach(p => {
                if (!p || !p.sid) return;
                let el = grid.querySelector(`[data-participant="${p.sid}"]`);
                if (!el) {
                    el = document.createElement('div');
                    el.className = 'relative rounded-xl overflow-hidden bg-surface-900 flex items-center justify-center cursor-pointer';
                    el.dataset.participant = p.sid;
                    el.addEventListener('click', () => this.togglePin(p.sid));

                    const label = document.createElement('div');
                    label.className = 'absolute bottom-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded-md flex items-center gap-2';
                    label.innerHTML = `<span>${p.name || p.identity || 'Anonymous'}</span>`;
                    el.appendChild(label);

                    grid.appendChild(el);
                }
                el.classList.toggle('ring-2', this.pinnedSid === p.sid);
                el.classList.toggle('ring-brand-500', this.pinnedSid === p.sid);
            });

            const sids = new Set(participants.filter(p => p && p.sid).map(p => p.sid));
            grid.querySelectorAll('[data-participant]').forEach(el => {
                if (!sids.has(el.dataset.participant)) el.remove();
            });
        },

        attachTrack(track, participant) {
            const stage = document.getElementById('livekit-stage');
            if (!stage) return;
            const grid = stage.querySelector('.participants-grid');
            if (!grid) return;

            const isScreenShare = track.source === 'screen_share' || track.source === 'screen_share_audio';
            const containerId = isScreenShare ? `${participant.sid}-screen` : participant.sid;

            let el = grid.querySelector(`[data-participant="${containerId}"]`);
            if (!el) {
                el = document.createElement('div');
                el.className = 'relative rounded-xl overflow-hidden bg-surface-900 flex items-center justify-center';
                if (!isScreenShare) {
                    el.classList.add('cursor-pointer');
                    el.addEventListener('click', () => this.togglePin(participant.sid));
                }
                el.dataset.participant = containerId;
                const label = document.createElement('div');
                label.className = 'absolute bottom-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded-md';
                const labelText = isScreenShare ? `${participant.name || participant.identity || 'Anonymous'}'s Screen` : (participant.name || participant.identity || 'Anonymous');
                label.innerHTML = `<span>${labelText}</span>`;
                el.appendChild(label);
                grid.appendChild(el);
            }

            const existing = el.querySelector(`[data-track-sid="${track.sid}"]`);
            if (existing) existing.remove();

            const mediaEl = track.attach();
            mediaEl.className = 'absolute inset-0 w-full h-full object-cover';
            mediaEl.dataset.trackSid = track.sid;
            if (track.kind === 'video') {
                mediaEl.setAttribute('playsinline', '');
                mediaEl.muted = true;
            }
            el.appendChild(mediaEl);
            if (mediaEl instanceof HTMLVideoElement) {
                mediaEl.play().catch(() => {});
            }
        },

        disconnect() {
            if (this.room) { this.room.disconnect(); this.room = null; }
            this.connected = false;
            this.sharingScreen = false;
            window.__livekitRoom = null;
            sessionStorage.removeItem('livekit_session');
            const stage = document.getElementById('livekit-stage');
            const grid = stage?.querySelector('.participants-grid');
            if (grid) grid.remove();
        },

        async toggleMic() {
            this.micEnabled = !this.micEnabled;
            const track = await this.room?.localParticipant.setMicrophoneEnabled(this.micEnabled);
            if (track) this.attachTrack(track, this.room.localParticipant);
        },

        async toggleCam() {
            this.camEnabled = !this.camEnabled;
            const track = await this.room?.localParticipant.setCameraEnabled(this.camEnabled);
            if (track) this.attachTrack(track, this.room.localParticipant);
        },

        toggleFullscreen() {
            const el = document.getElementById('livekit-stage');
            if (!el) return;
            if (!document.fullscreenElement) {
                el.requestFullscreen().then(() => this.fullscreen = true).catch(() => {});
            } else {
                document.exitFullscreen().then(() => this.fullscreen = false).catch(() => {});
            }
        },

        toggleScreenShare() {
            if (!this.room) return;
            this.sharingScreen = !this.sharingScreen;
            this.room.localParticipant.setScreenShareEnabled(this.sharingScreen).catch(() => {
                this.sharingScreen = false;
            });
        },

        togglePin(sid) {
            if (!sid) return;
            if (this.pinnedSid === sid) {
                this.pinnedSid = null;
            } else {
                this.pinnedSid = sid;
            }
            if (this.room) this.renderParticipants(this.room);
        },

        sendMessage() {
            const text = this.chatInput?.trim();
            if (!text || !this.room) return;
            const encoder = new TextEncoder();
            this.room.localParticipant.publishData(encoder.encode(JSON.stringify({ text })), {
                topic: 'chat',
            });
            this.chatMessages.push({ sender: 'You', text, isLocal: true });
            this.chatInput = '';
            this.$nextTick(() => {
                const box = this.$refs?.chatBox;
                if (box) box.scrollTop = box.scrollHeight;
            });
        },

        getCameraTrack() {
            const p = this.room?.localParticipant;
            if (!p) return null;
            if (!p.videoTrackPublications || typeof p.videoTrackPublications.forEach !== 'function') return null;
            let found = null;
            p.videoTrackPublications.forEach(pub => {
                if (pub.track?.kind === 'video' && pub.source !== 'screen_share') found = pub.track;
            });
            return found;
        },

        setBackgroundBlur() {
            if (!this.room?.localParticipant) return;
            const levels = [0, 1, 2];
            this.bgBlur = levels[(levels.indexOf(this.bgBlur) + 1) % levels.length];

            const track = this.getCameraTrack();
            if (!track) return;

            if (this.bgBlur === 0) {
                track.setProcessor(undefined).catch(() => {});
            } else {
                const blurRadius = this.bgBlur === 1 ? 4 : 12;
                const processor = LivekitProcessors.BackgroundBlur(blurRadius);
                track.setProcessor(processor).catch(() => {});
            }
        },

        cycleQuality() {
            const presets = ['Auto', '720p', '1080p'];
            const idx = presets.indexOf(this.videoQuality);
            this.videoQuality = presets[(idx + 1) % presets.length];

            const track = this.getCameraTrack();
            if (!track?.mediaStreamTrack) return;

            if (this.videoQuality === 'Auto') {
                track.mediaStreamTrack.applyConstraints({ width: { ideal: 1280 }, height: { ideal: 720 } }).catch(() => {});
            } else {
                const resolutions = { '720p': { width: { ideal: 1280 }, height: { ideal: 720 } }, '1080p': { width: { ideal: 1920 }, height: { ideal: 1080 } } };
                track.mediaStreamTrack.applyConstraints(resolutions[this.videoQuality]).catch(() => {});
            }
        },
    };
}
</script>
@endpush
