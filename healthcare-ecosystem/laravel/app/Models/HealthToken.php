<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'token_address',
        'token_name',
        'token_symbol',
        'balance',
        'locked_amount',
        'staked_amount',
        'rewards_earned',
        'last_reward_calculation',
        'token_metadata',
        'blockchain_network',
        'status'
    ];

    protected $casts = [
        'balance' => 'decimal:18',
        'locked_amount' => 'decimal:18',
        'staked_amount' => 'decimal:18',
        'rewards_earned' => 'decimal:18',
        'last_reward_calculation' => 'datetime',
        'token_metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }

    public function stakingPositions()
    {
        return $this->hasMany(StakingPosition::class);
    }

    public function calculateRewards()
    {
        // Implementation for calculating DeFi rewards based on health data and compliance
        $baseRate = 0.1; // 10% base APY
        $healthScore = $this->user->healthRecords()->latest()->first()->health_score ?? 0;
        $complianceBonus = $this->calculateComplianceBonus();
        
        $timeElapsed = now()->diffInDays($this->last_reward_calculation);
        $effectiveRate = ($baseRate + ($healthScore * 0.01) + $complianceBonus) / 365;
        
        $rewards = $this->staked_amount * $effectiveRate * $timeElapsed;
        
        $this->rewards_earned += $rewards;
        $this->last_reward_calculation = now();
        $this->save();
        
        return $rewards;
    }

    public function stake($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->staked_amount += $amount;
        $this->save();

        return $this->stakingPositions()->create([
            'amount' => $amount,
            'start_date' => now(),
            'apy_rate' => $this->calculateCurrentAPY()
        ]);
    }

    public function unstake($amount)
    {
        if ($this->staked_amount < $amount) {
            throw new \Exception('Insufficient staked amount');
        }

        $this->calculateRewards(); // Calculate final rewards before unstaking
        $this->staked_amount -= $amount;
        $this->balance += $amount;
        $this->save();
    }

    protected function calculateComplianceBonus()
    {
        // Calculate bonus based on user's compliance with health recommendations
        $totalRecommendations = $this->user->aiHealthPredictions()
            ->where('created_at', '>=', now()->subMonths(3))
            ->count();
            
        $followedRecommendations = $this->user->aiHealthPredictions()
            ->where('created_at', '>=', now()->subMonths(3))
            ->where('status', 'completed')
            ->count();

        if ($totalRecommendations === 0) return 0;

        return ($followedRecommendations / $totalRecommendations) * 0.05; // Up to 5% bonus
    }

    protected function calculateCurrentAPY()
    {
        $baseRate = 0.1; // 10% base APY
        $healthScore = $this->user->healthRecords()->latest()->first()->health_score ?? 0;
        $complianceBonus = $this->calculateComplianceBonus();
        
        return ($baseRate + ($healthScore * 0.01) + $complianceBonus) * 100; // Return as percentage
    }
} 