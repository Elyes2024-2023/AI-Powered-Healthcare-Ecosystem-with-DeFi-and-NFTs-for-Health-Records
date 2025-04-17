<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsurancePolicy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'policy_type',
        'coverage_amount',
        'premium_amount',
        'start_date',
        'end_date',
        'beneficiaries',
        'terms_and_conditions',
        'status',
        'smart_contract_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'beneficiaries' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the insurance policy.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include expired policies.
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Check if the policy is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    /**
     * Check if the policy is expired.
     */
    public function isExpired()
    {
        return $this->end_date < now();
    }

    /**
     * Check if the policy is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
} 