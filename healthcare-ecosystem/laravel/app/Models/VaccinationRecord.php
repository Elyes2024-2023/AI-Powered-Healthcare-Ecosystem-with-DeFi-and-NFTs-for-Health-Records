<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
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
        'smart_contract_address',
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
    ];

    /**
     * Get the user that owns the vaccination record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the health record associated with this vaccination.
     */
    public function healthRecord()
    {
        return $this->hasOne(HealthRecord::class);
    }

    /**
     * Get the insurance policy associated with this vaccination.
     */
    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
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
} 