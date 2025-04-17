<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SymptomRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'symptoms',
        'ai_diagnosis',
        'recommendations',
        'confidence_score',
        'requires_medical_attention',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ai_diagnosis' => 'array',
        'recommendations' => 'array',
        'confidence_score' => 'decimal:2',
        'requires_medical_attention' => 'boolean',
    ];

    /**
     * Get the user that owns the symptom record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 