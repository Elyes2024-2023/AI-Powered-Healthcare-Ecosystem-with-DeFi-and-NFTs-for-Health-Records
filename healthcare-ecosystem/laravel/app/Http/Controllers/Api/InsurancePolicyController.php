<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsurancePolicyController extends Controller
{
    /**
     * Display a listing of insurance policies for the authenticated user.
     */
    public function index()
    {
        $policies = InsurancePolicy::where('user_id', auth()->id())
            ->with(['user'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $policies
        ]);
    }

    /**
     * Store a newly created insurance policy.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policy_type' => 'required|string|in:health,life,disability',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'beneficiaries' => 'required|array',
            'beneficiaries.*.name' => 'required|string',
            'beneficiaries.*.relationship' => 'required|string',
            'beneficiaries.*.percentage' => 'required|numeric|min:0|max:100',
            'terms_and_conditions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $policy = InsurancePolicy::create([
                'user_id' => auth()->id(),
                'policy_type' => $request->policy_type,
                'coverage_amount' => $request->coverage_amount,
                'premium_amount' => $request->premium_amount,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'beneficiaries' => $request->beneficiaries,
                'terms_and_conditions' => $request->terms_and_conditions,
                'status' => 'active',
            ]);

            // TODO: Integrate with DeFi protocol for policy creation
            // This would involve creating a smart contract for the policy

            return response()->json([
                'status' => 'success',
                'message' => 'Insurance policy created successfully',
                'data' => $policy
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create insurance policy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified insurance policy.
     */
    public function show(InsurancePolicy $insurancePolicy)
    {
        if ($insurancePolicy->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $insurancePolicy->load('user')
        ]);
    }

    /**
     * Update the specified insurance policy.
     */
    public function update(Request $request, InsurancePolicy $insurancePolicy)
    {
        if ($insurancePolicy->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'policy_type' => 'sometimes|string|in:health,life,disability',
            'coverage_amount' => 'sometimes|numeric|min:0',
            'premium_amount' => 'sometimes|numeric|min:0',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'beneficiaries' => 'sometimes|array',
            'beneficiaries.*.name' => 'required_with:beneficiaries|string',
            'beneficiaries.*.relationship' => 'required_with:beneficiaries|string',
            'beneficiaries.*.percentage' => 'required_with:beneficiaries|numeric|min:0|max:100',
            'terms_and_conditions' => 'sometimes|string',
            'status' => 'sometimes|string|in:active,inactive,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $insurancePolicy->update($request->all());

            // TODO: Update the smart contract if policy details change

            return response()->json([
                'status' => 'success',
                'message' => 'Insurance policy updated successfully',
                'data' => $insurancePolicy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update insurance policy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified insurance policy.
     */
    public function destroy(InsurancePolicy $insurancePolicy)
    {
        if ($insurancePolicy->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            // TODO: Cancel the smart contract if policy is cancelled
            $insurancePolicy->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Insurance policy deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete insurance policy',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 