<?php
/**
 * Blockchain Service
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * BlockchainService
 * 
 * Handles all blockchain-related operations including token deployment,
 * transfers, and transaction status tracking.
 * 
 * @package App\Services
 * @author ELYES
 */
class BlockchainService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.blockchain.url');
        $this->apiKey = config('services.blockchain.api_key');
    }

    public function deployToken(array $data)
    {
        try {
            Log::info('Deploying token', [
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'network' => $data['network']
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->post($this->baseUrl . '/deploy-token', $data);

            if ($response->successful()) {
                Log::info('Token deployed successfully', [
                    'transaction_hash' => $response->json('transaction_hash'),
                    'token_address' => $response->json('token_address')
                ]);
                return $response->json();
            }

            Log::error('Token deployment failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            throw new \Exception('Token deployment failed: ' . $response->json('message'));
        } catch (\Exception $e) {
            Log::error('Token deployment exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function transferTokens(array $data)
    {
        try {
            Log::info('Transferring tokens', [
                'from' => $data['from_address'],
                'to' => $data['to_address'],
                'amount' => $data['amount']
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->post($this->baseUrl . '/transfer-token', $data);

            if ($response->successful()) {
                Log::info('Token transfer successful', [
                    'transaction_hash' => $response->json('transaction_hash')
                ]);
                return $response->json();
            }

            Log::error('Token transfer failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            throw new \Exception('Token transfer failed: ' . $response->json('message'));
        } catch (\Exception $e) {
            Log::error('Token transfer exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getTransactionStatus(string $transactionHash)
    {
        $cacheKey = 'transaction_status:' . $transactionHash;
        
        return Cache::remember($cacheKey, 60, function () use ($transactionHash) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey
                ])->get($this->baseUrl . '/transaction/' . $transactionHash);

                if ($response->successful()) {
                    return $response->json('status');
                }

                Log::error('Failed to get transaction status', [
                    'hash' => $transactionHash,
                    'status' => $response->status()
                ]);
                return 'unknown';
            } catch (\Exception $e) {
                Log::error('Transaction status check exception', [
                    'hash' => $transactionHash,
                    'error' => $e->getMessage()
                ]);
                return 'unknown';
            }
        });
    }
} 