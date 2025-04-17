<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'wallet_address',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the health records for the user.
     */
    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    /**
     * Get the insurance policies for the user.
     */
    public function insurancePolicies()
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    /**
     * Get the insurance claims for the user.
     */
    public function insuranceClaims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    /**
     * Get the symptom records for the user.
     */
    public function symptomRecords()
    {
        return $this->hasMany(SymptomRecord::class);
    }

    /**
     * Get the IoT device data for the user.
     */
    public function iotDeviceData()
    {
        return $this->hasMany(IotDeviceData::class);
    }
} 