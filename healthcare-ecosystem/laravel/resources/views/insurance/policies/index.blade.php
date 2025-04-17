<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Insurance Policies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Your Insurance Policies</h3>
                    <a href="{{ route('insurance.policies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create New Policy
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if($policies->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-gray-500">You don't have any insurance policies yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Policy Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Premium Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($policies as $policy)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $policy->policy_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($policy->coverage_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($policy->premium_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $policy->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($policy->status === 'expired' ? 'bg-red-100 text-red-800' : 
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($policy->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('insurance.policies.show', $policy) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            @if($policy->isActive())
                                                <a href="{{ route('insurance.policies.edit', $policy) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                <form action="{{ route('insurance.policies.cancel', $policy) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this policy?')">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $policies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 