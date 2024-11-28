<?php

namespace JaOcero\FilaChat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FilaChatUserTypingEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $type = FilaChatUserTypingEvent::class;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $conversationId,
        public bool $isTyping,
        public int $receiverId,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new Channel('filachat');
    }
}
