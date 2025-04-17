<?php
/**
 * Token Deployed Event
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

namespace App\Events;

use App\Models\HealthToken;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TokenDeployed
 * 
 * Event fired when a new health token is deployed to the blockchain.
 * 
 * @package App\Events
 * @author ELYES
 */
class TokenDeployed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $token;

    /**
     * Create a new event instance.
     */
    public function __construct(HealthToken $token)
    {
        $this->token = $token;
    }
} 