<?php

namespace App\Http\Controllers;

use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InsurancePolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $policies = InsurancePolicy::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('insurance.policies.index', compact('policies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('insurance.policies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policy_type' => 'required|string|max:255',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'beneficiaries' => 'required|array',
            'beneficiaries.*' => 'required|string',
            'terms_and_conditions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $policy = new InsurancePolicy($request->all());
        $policy->user_id = Auth::id();
        $policy->status = 'active';
        $policy->save();

        return redirect()->route('insurance.policies.index')
            ->with('success', 'Insurance policy created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InsurancePolicy $policy)
    {
        $this->authorize('view', $policy);
        return view('insurance.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InsurancePolicy $policy)
    {
        $this->authorize('update', $policy);
        return view('insurance.policies.edit', compact('policy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InsurancePolicy $policy)
    {
        $this->authorize('update', $policy);

        $validator = Validator::make($request->all(), [
            'policy_type' => 'required|string|max:255',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'beneficiaries' => 'required|array',
            'beneficiaries.*' => 'required|string',
            'terms_and_conditions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $policy->update($request->all());

        return redirect()->route('insurance.policies.index')
            ->with('success', 'Insurance policy updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InsurancePolicy $policy)
    {
        $this->authorize('delete', $policy);
        
        $policy->delete();

        return redirect()->route('insurance.policies.index')
            ->with('success', 'Insurance policy deleted successfully.');
    }

    /**
     * Cancel the specified insurance policy.
     */
    public function cancel(InsurancePolicy $policy)
    {
        $this->authorize('update', $policy);

        if ($policy->isActive()) {
            $policy->update(['status' => 'cancelled']);
            return redirect()->route('insurance.policies.index')
                ->with('success', 'Insurance policy cancelled successfully.');
        }

        return redirect()->route('insurance.policies.index')
            ->with('error', 'Cannot cancel this insurance policy.');
    }
} 