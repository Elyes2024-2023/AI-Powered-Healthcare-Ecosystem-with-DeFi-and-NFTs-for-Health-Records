<?php

namespace App\Http\Controllers;

use App\Models\VaccinationRecord;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VaccinationRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vaccinations = VaccinationRecord::where('user_id', Auth::id())
            ->with(['insurancePolicy'])
            ->latest()
            ->paginate(10);

        return view('vaccination.records.index', compact('vaccinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $policies = InsurancePolicy::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('vaccination.records.create', compact('policies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vaccine_name' => 'required|string|max:255',
            'vaccine_batch' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'vaccination_date' => 'required|date',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'vaccination_center' => 'required|string|max:255',
            'healthcare_provider' => 'required|string|max:255',
            'vaccination_proof' => 'required|string|max:255',
            'side_effects' => 'nullable|array',
            'notes' => 'nullable|array',
            'insurance_policy_id' => 'nullable|exists:insurance_policies,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vaccination = new VaccinationRecord($request->all());
        $vaccination->user_id = Auth::id();
        $vaccination->save();

        return redirect()->route('vaccination.records.index')
            ->with('success', 'Vaccination record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VaccinationRecord $record)
    {
        $this->authorize('view', $record);
        return view('vaccination.records.show', compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VaccinationRecord $record)
    {
        $this->authorize('update', $record);
        
        $policies = InsurancePolicy::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('vaccination.records.edit', compact('record', 'policies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VaccinationRecord $record)
    {
        $this->authorize('update', $record);

        $validator = Validator::make($request->all(), [
            'vaccine_name' => 'required|string|max:255',
            'vaccine_batch' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'vaccination_date' => 'required|date',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'vaccination_center' => 'required|string|max:255',
            'healthcare_provider' => 'required|string|max:255',
            'vaccination_proof' => 'required|string|max:255',
            'side_effects' => 'nullable|array',
            'notes' => 'nullable|array',
            'insurance_policy_id' => 'nullable|exists:insurance_policies,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $record->update($request->all());

        return redirect()->route('vaccination.records.index')
            ->with('success', 'Vaccination record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VaccinationRecord $record)
    {
        $this->authorize('delete', $record);
        
        $record->delete();

        return redirect()->route('vaccination.records.index')
            ->with('success', 'Vaccination record deleted successfully.');
    }

    /**
     * Get vaccination statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_vaccinations' => VaccinationRecord::where('user_id', Auth::id())->count(),
            'completed_vaccinations' => VaccinationRecord::where('user_id', Auth::id())
                ->where(function($query) {
                    $query->whereNull('next_dose_date')
                          ->orWhere('next_dose_date', '<', now());
                })->count(),
            'pending_vaccinations' => VaccinationRecord::where('user_id', Auth::id())
                ->whereNotNull('next_dose_date')
                ->where('next_dose_date', '>', now())
                ->count(),
            'vaccine_types' => VaccinationRecord::where('user_id', Auth::id())
                ->distinct('vaccine_name')
                ->pluck('vaccine_name'),
        ];

        return view('vaccination.records.statistics', compact('stats'));
    }
} 