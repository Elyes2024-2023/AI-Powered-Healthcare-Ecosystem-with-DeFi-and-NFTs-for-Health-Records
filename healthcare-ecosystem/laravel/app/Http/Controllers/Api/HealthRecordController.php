<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HealthRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $healthRecords = auth()->user()->healthRecords;

        return response()->json([
            'status' => 'success',
            'data' => [
                'health_records' => $healthRecords,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ipfs_hash' => ['required', 'string'],
            'encryption_key' => ['required', 'string'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $healthRecord = auth()->user()->healthRecords()->create([
            'ipfs_hash' => $request->ipfs_hash,
            'encryption_key' => $request->encryption_key,
            'metadata' => $request->metadata,
        ]);

        // TODO: Mint NFT for the health record
        // This will be implemented when we integrate with the blockchain

        return response()->json([
            'status' => 'success',
            'message' => 'Health record created successfully',
            'data' => [
                'health_record' => $healthRecord,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HealthRecord  $healthRecord
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(HealthRecord $healthRecord)
    {
        // Check if the user is authorized to view this health record
        if ($healthRecord->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'health_record' => $healthRecord,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HealthRecord  $healthRecord
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, HealthRecord $healthRecord)
    {
        // Check if the user is authorized to update this health record
        if ($healthRecord->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ipfs_hash' => ['required', 'string'],
            'encryption_key' => ['required', 'string'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $healthRecord->update([
            'ipfs_hash' => $request->ipfs_hash,
            'encryption_key' => $request->encryption_key,
            'metadata' => $request->metadata,
        ]);

        // TODO: Update NFT for the health record
        // This will be implemented when we integrate with the blockchain

        return response()->json([
            'status' => 'success',
            'message' => 'Health record updated successfully',
            'data' => [
                'health_record' => $healthRecord,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HealthRecord  $healthRecord
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(HealthRecord $healthRecord)
    {
        // Check if the user is authorized to delete this health record
        if ($healthRecord->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        // TODO: Burn NFT for the health record
        // This will be implemented when we integrate with the blockchain

        $healthRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Health record deleted successfully',
        ]);
    }
} 