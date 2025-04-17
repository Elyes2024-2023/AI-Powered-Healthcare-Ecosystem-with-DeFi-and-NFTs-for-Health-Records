<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Insurance Policy Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Policy Information</h3>
                    <div class="flex space-x-4">
                        @if($policy->isActive())
                            <a href="{{ route('insurance.policies.edit', $policy) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Edit Policy
                            </a>
                            <form action="{{ route('insurance.policies.cancel', $policy) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to cancel this policy?')">
                                    Cancel Policy
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('insurance.policies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-4">Basic Information</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Policy Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $policy->policy_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $policy->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($policy->status === 'expired' ? 'bg-red-100 text-red-800' : 
                                           'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($policy->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Coverage Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">${{ number_format($policy->coverage_amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Premium Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">${{ number_format($policy->premium_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-4">Policy Period</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $policy->start_date->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $policy->end_date->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $policy->start_date->diffInDays($policy->end_date) }} days</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-semibold mb-4">Beneficiaries</h4>
                        <ul class="list-disc list-inside">
                            @foreach($policy->beneficiaries as $beneficiary)
                                <li class="text-sm text-gray-900">{{ $beneficiary }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-semibold mb-4">Terms and Conditions</h4>
                        <div class="prose max-w-none">
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $policy->terms_and_conditions }}</p>
                        </div>
                    </div>

                    @if($policy->smart_contract_address)
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h4 class="font-semibold mb-4">Smart Contract Information</h4>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Smart Contract Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $policy->smart_contract_address }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 