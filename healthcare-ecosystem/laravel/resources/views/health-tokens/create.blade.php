{{-- 
    Health Tokens Create View
    @copyright ELYES 2024-2025
    @author ELYES
    @package Healthcare-Ecosystem
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Create New Health Token</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('health-tokens.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="token_name" class="form-label">Token Name</label>
                            <input type="text" class="form-control @error('token_name') is-invalid @enderror" 
                                id="token_name" name="token_name" value="{{ old('token_name') }}" required>
                            @error('token_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="token_symbol" class="form-label">Token Symbol</label>
                            <input type="text" class="form-control @error('token_symbol') is-invalid @enderror" 
                                id="token_symbol" name="token_symbol" value="{{ old('token_symbol') }}" required>
                            @error('token_symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="blockchain_network" class="form-label">Blockchain Network</label>
                            <select class="form-select @error('blockchain_network') is-invalid @enderror" 
                                id="blockchain_network" name="blockchain_network" required>
                                <option value="">Select Network</option>
                                <option value="ethereum" {{ old('blockchain_network') == 'ethereum' ? 'selected' : '' }}>Ethereum</option>
                                <option value="polygon" {{ old('blockchain_network') == 'polygon' ? 'selected' : '' }}>Polygon</option>
                                <option value="binance" {{ old('blockchain_network') == 'binance' ? 'selected' : '' }}>Binance Smart Chain</option>
                            </select>
                            @error('blockchain_network')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total_supply" class="form-label">Total Supply</label>
                            <input type="number" class="form-control @error('total_supply') is-invalid @enderror" 
                                id="total_supply" name="total_supply" value="{{ old('total_supply') }}" required>
                            @error('total_supply')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="decimals" class="form-label">Decimals</label>
                            <input type="number" class="form-control @error('decimals') is-invalid @enderror" 
                                id="decimals" name="decimals" value="{{ old('decimals', 18) }}" required>
                            @error('decimals')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_stakeable" name="is_stakeable" 
                                    {{ old('is_stakeable') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_stakeable">
                                    Enable Staking
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Token</button>
                            <a href="{{ route('health-tokens.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 