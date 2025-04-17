<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiHealthPrediction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'health_record_id',
        'prediction_type',
        'prediction_result',
        'confidence_score',
        'input_parameters',
        'model_version',
        'recommendations',
        'risk_factors',
        'next_checkup_date',
        'ai_model_metadata',
        'status'
    ];

    protected $casts = [
        'prediction_result' => 'array',
        'input_parameters' => 'array',
        'recommendations' => 'array',
        'risk_factors' => 'array',
        'ai_model_metadata' => 'array',
        'next_checkup_date' => 'datetime',
        'confidence_score' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthRecord()
    {
        return $this->belongsTo(HealthRecord::class);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('confidence_score', '>=', 0.8);
    }

    public function scopeRecentPredictions($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    public function generateNFTMetadata()
    {
        return [
            'name' => "AI Health Prediction #{$this->id}",
            'description' => "AI-generated health prediction for {$this->user->name}",
            'attributes' => [
                [
                    'trait_type' => 'Prediction Type',
                    'value' => $this->prediction_type
                ],
                [
                    'trait_type' => 'Confidence Score',
                    'value' => $this->confidence_score
                ],
                [
                    'trait_type' => 'Model Version',
                    'value' => $this->model_version
                ],
                [
                    'trait_type' => 'Status',
                    'value' => $this->status
                ]
            ],
            'properties' => [
                'risk_factors' => $this->risk_factors,
                'recommendations' => $this->recommendations
            ]
        ];
    }
} 