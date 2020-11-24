<?php

namespace Qihucms\UserTask\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Qihucms\UserTask\Models\UserTaskOrder;

class TaskCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $taskOrder;

    /**
     * Create a new event instance.
     *
     * @param UserTaskOrder $taskOrder
     * @return void
     */
    public function __construct(UserTaskOrder $taskOrder)
    {
        $this->taskOrder = $taskOrder;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('task.order.' . $this->taskOrder->user_id);
    }
}
