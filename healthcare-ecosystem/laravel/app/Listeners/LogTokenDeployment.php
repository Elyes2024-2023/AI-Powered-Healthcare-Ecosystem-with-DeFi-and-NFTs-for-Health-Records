<?php
/**
 * Log Token Deployment Listener
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

namespace App\Listeners;

use App\Events\TokenDeployed;
use Illuminate\Support\Facades\Log;

/**
 * LogTokenDeployment
 * 
 * Listener for logging token deployment events.
 * 
 * @package App\Listeners
 * @author ELYES
 */
class LogTokenDeployment
{
    /**
     * Handle the event.
     */
    public function handle(TokenDeployed $event): void
    {
        Log::info('Health token deployed', [
            'token_id' => $event->token->id,
            'token_name' => $event->token->token_name,
            'token_symbol' => $event->token->token_symbol,
            'network' => $event->token->blockchain_network,
            'user_id' => $event->token->user_id
        ]);
    }
} 