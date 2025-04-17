<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IotDeviceData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'device_type',
        'data',
        'recorded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the user that owns the IoT device data.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 