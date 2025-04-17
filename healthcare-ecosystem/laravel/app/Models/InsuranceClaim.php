<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'insurance_policy_id',
        'claim_number',
        'smart_contract_address',
        'claim_amount',
        'currency',
        'description',
        'supporting_documents',
        'status',
        'rejection_reason',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'supporting_documents' => 'array',
        'claim_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the insurance claim.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insurance policy that the claim belongs to.
     */
    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
    }
} 