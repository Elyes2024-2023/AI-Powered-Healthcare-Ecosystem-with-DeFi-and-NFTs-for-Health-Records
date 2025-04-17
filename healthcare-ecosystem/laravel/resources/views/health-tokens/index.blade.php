{{-- 
    Health Tokens Index View
    @copyright ELYES 2024-2025
    @author ELYES
    @package Healthcare-Ecosystem
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Health Tokens</h2>
                    <a href="{{ route('health-tokens.create') }}" class="btn btn-primary">Create New Token</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($tokens->isEmpty())
                        <div class="text-center py-4">
                            <p class="lead">No health tokens found.</p>
                            <p>Create your first health token to start managing your healthcare assets.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Token Name</th>
                                        <th>Symbol</th>
                                        <th>Network</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tokens as $token)
                                        <tr>
                                            <td>{{ $token->token_name }}</td>
                                            <td>{{ $token->token_symbol }}</td>
                                            <td>{{ $token->blockchain_network }}</td>
                                            <td>{{ number_format($token->balance, 6) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $token->status === 'active' ? 'success' : ($token->status === 'paused' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($token->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $token->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('health-tokens.show', $token) }}" class="btn btn-sm btn-info">View</a>
                                                    <a href="{{ route('health-tokens.edit', $token) }}" class="btn btn-sm btn-warning">Edit</a>
                                                    <form action="{{ route('health-tokens.destroy', $token) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this token?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $tokens->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 