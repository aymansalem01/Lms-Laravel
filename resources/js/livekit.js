export async function loadLiveKit() {
    if (window.LivekitClient) return;
    const [{ Room, RoomEvent, createLocalVideoTrack }, { BackgroundBlur }] = await Promise.all([
        import('livekit-client'),
        import('@livekit/track-processors'),
    ]);
    window.LivekitClient = { Room, RoomEvent, createLocalVideoTrack };
    window.LivekitProcessors = { BackgroundBlur };
}

window.loadLiveKit = loadLiveKit;
