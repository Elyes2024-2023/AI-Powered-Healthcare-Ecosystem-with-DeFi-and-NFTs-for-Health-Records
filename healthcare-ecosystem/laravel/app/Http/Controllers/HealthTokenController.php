<?php
/**
 * Health Token Controller
 * 
 * @copyright ELYES 2024-2025
 * @author ELYES
 * @package Healthcare-Ecosystem
 */

namespace App\Http\Controllers;

use App\Models\HealthToken;
use App\Models\StakingPosition;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HealthTokenController
 * 
 * Handles all health token related operations including CRUD,
 * staking, unstaking, and token transfers.
 * 
 * @package App\Http\Controllers
 * @author ELYES
 */
class HealthTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tokens = HealthToken::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('health-tokens.index', compact('tokens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('health-tokens.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'token_name' => 'required|string|max:255',
            'token_symbol' => 'required|string|max:10',
            'blockchain_network' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        try {
            // Call blockchain service to deploy token
            $response = Http::post(env('BLOCKCHAIN_SERVICE_URL') . '/deploy-token', [
                'name' => $validated['token_name'],
                'symbol' => $validated['token_symbol'],
                'network' => $validated['blockchain_network'],
                'owner_address' => Auth::user()->wallet_address
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                
                // Create token record
                $token = HealthToken::create([
                    'user_id' => $validated['user_id'],
                    'token_address' => $tokenData['token_address'],
                    'token_name' => $validated['token_name'],
                    'token_symbol' => $validated['token_symbol'],
                    'blockchain_network' => $validated['blockchain_network'],
                    'token_metadata' => $tokenData['metadata'] ?? null,
                    'status' => 'active'
                ]);

                // Record initial transaction
                TokenTransaction::create([
                    'health_token_id' => $token->id,
                    'transaction_type' => 'mint',
                    'amount' => 0,
                    'from_address' => null,
                    'to_address' => Auth::user()->wallet_address,
                    'transaction_hash' => $tokenData['transaction_hash'],
                    'status' => 'completed'
                ]);

                return redirect()->route('health-tokens.show', $token)
                    ->with('success', 'Health token created successfully.');
            } else {
                Log::error('Blockchain service error: ' . $response->body());
                return back()->with('error', 'Failed to create health token. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Blockchain service exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while creating health token.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthToken $token)
    {
        $this->authorize('view', $token);

        $transactions = $token->transactions()->latest()->take(10)->get();
        $stakingPositions = $token->stakingPositions()->latest()->take(5)->get();

        return view('health-tokens.show', compact('token', 'transactions', 'stakingPositions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthToken $token)
    {
        $this->authorize('update', $token);

        return view('health-tokens.edit', compact('token'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HealthToken $token)
    {
        $this->authorize('update', $token);

        $validated = $request->validate([
            'token_name' => 'required|string|max:255',
            'token_symbol' => 'required|string|max:10',
            'status' => 'required|string|in:active,inactive,paused',
        ]);

        $token->update($validated);

        return redirect()->route('health-tokens.show', $token)
            ->with('success', 'Health token updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthToken $token)
    {
        $this->authorize('delete', $token);

        $token->delete();

        return redirect()->route('health-tokens.index')
            ->with('success', 'Health token deleted successfully.');
    }

    /**
     * Stake tokens.
     */
    public function stake(Request $request, HealthToken $token)
    {
        $this->authorize('update', $token);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.000001',
        ]);

        try {
            $token->stake($validated['amount']);

            return redirect()->route('health-tokens.show', $token)
                ->with('success', 'Tokens staked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unstake tokens.
     */
    public function unstake(Request $request, HealthToken $token)
    {
        $this->authorize('update', $token);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.000001',
        ]);

        try {
            $token->unstake($validated['amount']);

            return redirect()->route('health-tokens.show', $token)
                ->with('success', 'Tokens unstaked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Calculate rewards.
     */
    public function calculateRewards(HealthToken $token)
    {
        $this->authorize('update', $token);

        try {
            $rewards = $token->calculateRewards();

            return redirect()->route('health-tokens.show', $token)
                ->with('success', "Rewards calculated: {$rewards} tokens.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Transfer tokens.
     */
    public function transfer(Request $request, HealthToken $token)
    {
        $this->authorize('update', $token);

        $validated = $request->validate([
            'to_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.000001',
        ]);

        try {
            // Call blockchain service to transfer tokens
            $response = Http::post(env('BLOCKCHAIN_SERVICE_URL') . '/transfer-token', [
                'token_address' => $token->token_address,
                'from_address' => Auth::user()->wallet_address,
                'to_address' => $validated['to_address'],
                'amount' => $validated['amount']
            ]);

            if ($response->successful()) {
                $transferData = $response->json();
                
                // Record transaction
                TokenTransaction::create([
                    'health_token_id' => $token->id,
                    'transaction_type' => 'transfer',
                    'amount' => $validated['amount'],
                    'from_address' => Auth::user()->wallet_address,
                    'to_address' => $validated['to_address'],
                    'transaction_hash' => $transferData['transaction_hash'],
                    'status' => 'completed'
                ]);

                // Update token balance
                $token->balance -= $validated['amount'];
                $token->save();

                return redirect()->route('health-tokens.show', $token)
                    ->with('success', 'Tokens transferred successfully.');
            } else {
                Log::error('Blockchain service error: ' . $response->body());
                return back()->with('error', 'Failed to transfer tokens. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Blockchain service exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while transferring tokens.');
        }
    }
} 