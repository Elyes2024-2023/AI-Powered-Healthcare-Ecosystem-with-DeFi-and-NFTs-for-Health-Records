<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SymptomRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SymptomCheckerController extends Controller
{
    /**
     * Analyze symptoms using AI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptoms' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Call the AI service to analyze symptoms
            $response = Http::post(config('services.ai.url') . '/analyze-symptoms', [
                'symptoms' => $request->symptoms,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to analyze symptoms',
                    'errors' => $response->json(),
                ], 500);
            }

            $aiResponse = $response->json();

            // Create a symptom record
            $symptomRecord = auth()->user()->symptomRecords()->create([
                'symptoms' => $request->symptoms,
                'ai_diagnosis' => $aiResponse['diagnosis'] ?? null,
                'recommendations' => $aiResponse['recommendations'] ?? null,
                'confidence_score' => $aiResponse['confidence_score'] ?? null,
                'requires_medical_attention' => $aiResponse['requires_medical_attention'] ?? false,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Symptoms analyzed successfully',
                'data' => [
                    'symptom_record' => $symptomRecord,
                    'ai_analysis' => $aiResponse,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while analyzing symptoms',
                'errors' => [
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Get the history of symptom records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function history()
    {
        $symptomRecords = auth()->user()->symptomRecords()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'symptom_records' => $symptomRecords,
            ],
        ]);
    }
} 