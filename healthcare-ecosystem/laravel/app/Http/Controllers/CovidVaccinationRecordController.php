<?php

namespace App\Http\Controllers;

use App\Models\CovidVaccinationRecord;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

class CovidVaccinationRecordController extends Controller
{
    protected $web3;

    public function __construct()
    {
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager(env('ETHEREUM_NODE_URL'))));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = CovidVaccinationRecord::where('user_id', Auth::id())
            ->with(['insurancePolicy'])
            ->latest()
            ->paginate(10);

        return view('covid-vaccination.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $insurancePolicies = InsurancePolicy::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('covid-vaccination.create', compact('insurancePolicies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'insurance_policy_id' => 'required|exists:insurance_policies,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_batch' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'vaccination_date' => 'required|date',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'vaccination_center' => 'required|string|max:255',
            'healthcare_provider' => 'required|string|max:255',
            'vaccination_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'side_effects' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('vaccination_proof')) {
            $path = $request->file('vaccination_proof')->store('vaccination-proofs', 'public');
            $validated['vaccination_proof'] = $path;
        }

        $record = CovidVaccinationRecord::create($validated);

        // Generate and mint NFT
        $this->mintNFT($record);

        return redirect()->route('covid-vaccination.show', $record)
            ->with('success', 'COVID vaccination record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CovidVaccinationRecord $record)
    {
        $this->authorize('view', $record);

        return view('covid-vaccination.show', compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CovidVaccinationRecord $record)
    {
        $this->authorize('update', $record);

        $insurancePolicies = InsurancePolicy::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('covid-vaccination.edit', compact('record', 'insurancePolicies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CovidVaccinationRecord $record)
    {
        $this->authorize('update', $record);

        $validated = $request->validate([
            'insurance_policy_id' => 'required|exists:insurance_policies,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_batch' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1',
            'vaccination_date' => 'required|date',
            'next_dose_date' => 'nullable|date|after:vaccination_date',
            'vaccination_center' => 'required|string|max:255',
            'healthcare_provider' => 'required|string|max:255',
            'vaccination_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'side_effects' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        if ($request->hasFile('vaccination_proof')) {
            // Delete old proof
            if ($record->vaccination_proof) {
                Storage::disk('public')->delete($record->vaccination_proof);
            }
            
            $path = $request->file('vaccination_proof')->store('vaccination-proofs', 'public');
            $validated['vaccination_proof'] = $path;
        }

        $record->update($validated);

        // Update NFT metadata if needed
        if ($record->hasNFT()) {
            $this->updateNFTMetadata($record);
        }

        return redirect()->route('covid-vaccination.show', $record)
            ->with('success', 'COVID vaccination record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CovidVaccinationRecord $record)
    {
        $this->authorize('delete', $record);

        if ($record->vaccination_proof) {
            Storage::disk('public')->delete($record->vaccination_proof);
        }

        $record->delete();

        return redirect()->route('covid-vaccination.index')
            ->with('success', 'COVID vaccination record deleted successfully.');
    }

    /**
     * Display vaccination statistics.
     */
    public function statistics()
    {
        $userId = Auth::id();
        
        $statistics = [
            'total_vaccinations' => CovidVaccinationRecord::where('user_id', $userId)->count(),
            'completed_vaccinations' => CovidVaccinationRecord::where('user_id', $userId)
                ->where(function ($query) {
                    $query->whereNull('next_dose_date')
                        ->orWhere('next_dose_date', '<', now());
                })->count(),
            'pending_vaccinations' => CovidVaccinationRecord::where('user_id', $userId)
                ->where('next_dose_date', '>', now())->count(),
            'vaccine_types' => CovidVaccinationRecord::where('user_id', $userId)
                ->distinct('vaccine_name')->pluck('vaccine_name'),
            'nft_count' => CovidVaccinationRecord::where('user_id', $userId)
                ->whereNotNull('nft_token_id')->count(),
            'insurance_coverage' => CovidVaccinationRecord::where('user_id', $userId)
                ->whereNotNull('coverage_amount')->sum('coverage_amount'),
        ];

        return view('covid-vaccination.statistics', compact('statistics'));
    }

    /**
     * Mint NFT for vaccination record.
     */
    protected function mintNFT(CovidVaccinationRecord $record)
    {
        try {
            // Generate metadata
            $metadata = $record->generateNFTMetadata();
            
            // Upload to IPFS (implementation needed)
            $ipfsHash = $this->uploadToIPFS($metadata);
            
            // Mint NFT using smart contract (implementation needed)
            $tokenId = $this->mintNFTToken($ipfsHash);
            
            // Update record with NFT details
            $record->update([
                'nft_token_id' => $tokenId,
                'ipfs_hash' => $ipfsHash,
                'metadata' => $metadata,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('NFT minting failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update NFT metadata.
     */
    protected function updateNFTMetadata(CovidVaccinationRecord $record)
    {
        try {
            $metadata = $record->generateNFTMetadata();
            
            // Update metadata on IPFS (implementation needed)
            $ipfsHash = $this->uploadToIPFS($metadata);
            
            // Update token URI in smart contract (implementation needed)
            $this->updateTokenURI($record->nft_token_id, $ipfsHash);
            
            $record->update([
                'ipfs_hash' => $ipfsHash,
                'metadata' => $metadata,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('NFT metadata update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload metadata to IPFS.
     */
    protected function uploadToIPFS($metadata)
    {
        // Implementation needed: Upload metadata to IPFS
        // Return IPFS hash
        return 'ipfs://' . hash('sha256', json_encode($metadata));
    }

    /**
     * Mint NFT token using smart contract.
     */
    protected function mintNFTToken($ipfsHash)
    {
        // Implementation needed: Call smart contract to mint NFT
        // Return token ID
        return uniqid();
    }

    /**
     * Update token URI in smart contract.
     */
    protected function updateTokenURI($tokenId, $ipfsHash)
    {
        // Implementation needed: Call smart contract to update token URI
    }
} 