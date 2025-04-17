<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CovidVaccinationRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'nft_token_id',
        'ipfs_hash',
        'encryption_key',
        'metadata',
        'smart_contract_address',
        'coverage_amount',
        'blockchain_transaction_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vaccination_date' => 'date',
        'next_dose_date' => 'date',
        'side_effects' => 'array',
        'notes' => 'array',
        'metadata' => 'array',
        'coverage_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the vaccination record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insurance policy associated with this vaccination.
     */
    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
    }

    /**
     * Get the health record associated with this vaccination.
     */
    public function healthRecord()
    {
        return $this->hasOne(HealthRecord::class);
    }

    /**
     * Scope a query to only include records for a specific vaccine.
     */
    public function scopeForVaccine($query, $vaccineName)
    {
        return $query->where('vaccine_name', $vaccineName);
    }

    /**
     * Scope a query to only include records that need follow-up.
     */
    public function scopeNeedsFollowUp($query)
    {
        return $query->whereNotNull('next_dose_date')
                    ->where('next_dose_date', '>', now());
    }

    /**
     * Check if the vaccination is complete.
     */
    public function isComplete()
    {
        return $this->next_dose_date === null || $this->next_dose_date < now();
    }

    /**
     * Get the days until next dose.
     */
    public function daysUntilNextDose()
    {
        if (!$this->next_dose_date) {
            return 0;
        }
        return now()->diffInDays($this->next_dose_date, false);
    }

    /**
     * Check if the vaccination record has an NFT.
     */
    public function hasNFT()
    {
        return !empty($this->nft_token_id);
    }

    /**
     * Check if the vaccination record is covered by insurance.
     */
    public function isInsured()
    {
        return !empty($this->insurance_policy_id) && !empty($this->coverage_amount);
    }

    /**
     * Get the blockchain transaction status.
     */
    public function getBlockchainStatus()
    {
        if (empty($this->blockchain_transaction_hash)) {
            return 'pending';
        }
        return 'confirmed';
    }

    /**
     * Generate metadata for NFT creation.
     */
    public function generateNFTMetadata()
    {
        return [
            'name' => "COVID-19 Vaccination Record - {$this->vaccine_name}",
            'description' => "Vaccination record for {$this->user->name}",
            'attributes' => [
                'vaccine_name' => $this->vaccine_name,
                'vaccine_batch' => $this->vaccine_batch,
                'dose_number' => $this->dose_number,
                'vaccination_date' => $this->vaccination_date->format('Y-m-d'),
                'vaccination_center' => $this->vaccination_center,
                'healthcare_provider' => $this->healthcare_provider,
            ],
            'image' => $this->vaccination_proof,
        ];
    }
} 