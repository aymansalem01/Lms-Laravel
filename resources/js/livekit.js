import { Room, RoomEvent, VideoPresets } from 'livekit-client';

export async function connectToLiveKit(roomName, token, host) {
    const room = new Room({
        adaptiveStream: true,
        dynacast: true,
        videoCaptureDefaults: {
            resolution: VideoPresets.h720.resolution,
        },
    });

    await room.connect(host, token);

    await room.localParticipant.enableCameraAndMicrophone();

    return room;
}

export { RoomEvent };
