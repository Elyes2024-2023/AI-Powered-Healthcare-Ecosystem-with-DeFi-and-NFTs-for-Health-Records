<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'insurance_policy_id',
        'vaccine_name',
        'vaccine_batch',
        'dose_number',
        'vaccination_date',
        'next_dose_date',
        'vaccination_center',
        'healthcare_provider',
        'vaccination_proof',
        'side_effects',
        'notes',
        'status',
        'nft_token_id',
        'ipfs_hash',
        'metadata',
        'blockchain_transaction_hash',
    ];

    protected $casts = [
        'vaccination_date' => 'datetime',
        'next_dose_date' => 'datetime',
        'side_effects' => 'array',
        'notes' => 'array',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function hasNFT()
    {
        return !is_null($this->nft_token_id);
    }

    public function generateNFTMetadata()
    {
        return [
            'name' => "COVID-19 Vaccination Record #{$this->id}",
            'description' => "Vaccination record for {$this->user->name}",
            'image' => $this->vaccination_proof,
            'attributes' => [
                [
                    'trait_type' => 'Vaccine Name',
                    'value' => $this->vaccine_name
                ],
                [
                    'trait_type' => 'Dose Number',
                    'value' => $this->dose_number
                ],
                [
                    'trait_type' => 'Vaccination Date',
                    'value' => $this->vaccination_date->format('Y-m-d')
                ],
                [
                    'trait_type' => 'Vaccination Center',
                    'value' => $this->vaccination_center
                ],
                [
                    'trait_type' => 'Healthcare Provider',
                    'value' => $this->healthcare_provider
                ],
                [
                    'trait_type' => 'Status',
                    'value' => $this->status
                ]
            ]
        ];
    }
} 