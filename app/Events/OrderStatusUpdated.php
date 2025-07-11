<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
//use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('orders'); // Use PrivateChannel if needed for security
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'delivery_address' => $this->order->delivery_address,
            'total' => $this->order->total,
        ];
    }

    public function broadcastAs()
    {
        return 'OrderStatusUpdated';
    }
}