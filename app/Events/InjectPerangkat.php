<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\DetailPerangkat;

class InjectPerangkat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public  $detailPerangkat;
    public $hash_id;

    public function __construct($detailPerangkat, $hash_id)
    {
        // $this->detailPerangkat = $detailPerangkat;
        $this->detailPerangkat = json_decode(json_encode($detailPerangkat), true);
        $this->hash_id = $hash_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['perangkat.' . $this->hash_id];
    }
    public function broadcastAs()
    {
        return 'insertNew';
    }
}
