<?php

namespace App\Http\Controllers;

use App\Models\AiHealthPrediction;
use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiHealthPredictionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $predictions = AiHealthPrediction::where('user_id', Auth::id())
            ->with(['healthRecord'])
            ->latest()
            ->paginate(10);

        return view('ai-predictions.index', compact('predictions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $healthRecords = HealthRecord::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('ai-predictions.create', compact('healthRecords'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'health_record_id' => 'required|exists:health_records,id',
            'prediction_type' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        // Get health record data
        $healthRecord = HealthRecord::findOrFail($validated['health_record_id']);
        
        // Prepare data for AI service
        $healthData = [
            'age' => $healthRecord->age,
            'gender' => $healthRecord->gender,
            'bmi' => $healthRecord->bmi,
            'blood_pressure_systolic' => $healthRecord->blood_pressure_systolic,
            'blood_pressure_diastolic' => $healthRecord->blood_pressure_diastolic,
            'heart_rate' => $healthRecord->heart_rate,
            'temperature' => $healthRecord->temperature,
            'oxygen_saturation' => $healthRecord->oxygen_saturation,
        ];

        try {
            // Call AI service
            $response = Http::post(env('AI_SERVICE_URL') . '/predict', [
                'health_data' => $healthData,
                'prediction_type' => $validated['prediction_type']
            ]);

            if ($response->successful()) {
                $predictionData = $response->json();
                
                // Create prediction record
                $prediction = AiHealthPrediction::create([
                    'user_id' => $validated['user_id'],
                    'health_record_id' => $validated['health_record_id'],
                    'prediction_type' => $validated['prediction_type'],
                    'prediction_result' => $predictionData['prediction_result'],
                    'confidence_score' => $predictionData['confidence_score'],
                    'input_parameters' => $predictionData['input_parameters'],
                    'model_version' => $predictionData['model_version'],
                    'recommendations' => $predictionData['recommendations'],
                    'risk_factors' => $predictionData['risk_factors'],
                    'next_checkup_date' => $predictionData['next_checkup_date'],
                    'ai_model_metadata' => $predictionData['ai_model_metadata'],
                    'status' => 'pending'
                ]);

                return redirect()->route('ai-predictions.show', $prediction)
                    ->with('success', 'Health prediction generated successfully.');
            } else {
                Log::error('AI service error: ' . $response->body());
                return back()->with('error', 'Failed to generate health prediction. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('AI service exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating health prediction.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AiHealthPrediction $prediction)
    {
        $this->authorize('view', $prediction);

        return view('ai-predictions.show', compact('prediction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiHealthPrediction $prediction)
    {
        $this->authorize('update', $prediction);

        $healthRecords = HealthRecord::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('ai-predictions.edit', compact('prediction', 'healthRecords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiHealthPrediction $prediction)
    {
        $this->authorize('update', $prediction);

        $validated = $request->validate([
            'health_record_id' => 'required|exists:health_records,id',
            'prediction_type' => 'required|string|max:255',
            'status' => 'required|string|in:pending,completed,ignored',
        ]);

        $prediction->update($validated);

        return redirect()->route('ai-predictions.show', $prediction)
            ->with('success', 'Health prediction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiHealthPrediction $prediction)
    {
        $this->authorize('delete', $prediction);

        $prediction->delete();

        return redirect()->route('ai-predictions.index')
            ->with('success', 'Health prediction deleted successfully.');
    }

    /**
     * Generate NFT for the prediction.
     */
    public function generateNFT(AiHealthPrediction $prediction)
    {
        $this->authorize('update', $prediction);

        try {
            // Generate metadata
            $metadata = $prediction->generateNFTMetadata();
            
            // Call blockchain service to mint NFT
            $response = Http::post(env('BLOCKCHAIN_SERVICE_URL') . '/mint-nft', [
                'metadata' => $metadata,
                'owner_address' => Auth::user()->wallet_address
            ]);

            if ($response->successful()) {
                $nftData = $response->json();
                
                // Update prediction with NFT details
                $prediction->update([
                    'nft_token_id' => $nftData['token_id'],
                    'ipfs_hash' => $nftData['ipfs_hash'],
                    'blockchain_transaction_hash' => $nftData['transaction_hash']
                ]);

                return redirect()->route('ai-predictions.show', $prediction)
                    ->with('success', 'NFT generated successfully.');
            } else {
                Log::error('Blockchain service error: ' . $response->body());
                return back()->with('error', 'Failed to generate NFT. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Blockchain service exception: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while generating NFT.');
        }
    }
} 