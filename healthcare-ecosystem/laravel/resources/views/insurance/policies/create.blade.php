<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Insurance Policy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('insurance.policies.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-label for="policy_type" value="{{ __('Policy Type') }}" />
                            <x-input id="policy_type" class="block mt-1 w-full" type="text" name="policy_type" :value="old('policy_type')" required />
                            @error('policy_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="coverage_amount" value="{{ __('Coverage Amount') }}" />
                            <x-input id="coverage_amount" class="block mt-1 w-full" type="number" step="0.01" name="coverage_amount" :value="old('coverage_amount')" required />
                            @error('coverage_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="premium_amount" value="{{ __('Premium Amount') }}" />
                            <x-input id="premium_amount" class="block mt-1 w-full" type="number" step="0.01" name="premium_amount" :value="old('premium_amount')" required />
                            @error('premium_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="start_date" value="{{ __('Start Date') }}" />
                            <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="end_date" value="{{ __('End Date') }}" />
                            <x-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="beneficiaries" value="{{ __('Beneficiaries') }}" />
                            <div id="beneficiaries-container">
                                <div class="flex items-center mt-1">
                                    <x-input class="block w-full" type="text" name="beneficiaries[]" :value="old('beneficiaries.0')" required />
                                    <button type="button" onclick="addBeneficiary()" class="ml-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Add
                                    </button>
                                </div>
                            </div>
                            @error('beneficiaries')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="terms_and_conditions" value="{{ __('Terms and Conditions') }}" />
                            <textarea id="terms_and_conditions" name="terms_and_conditions" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" required>{{ old('terms_and_conditions') }}</textarea>
                            @error('terms_and_conditions')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Create Policy') }}
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let beneficiaryCount = 1;

        function addBeneficiary() {
            const container = document.getElementById('beneficiaries-container');
            const div = document.createElement('div');
            div.className = 'flex items-center mt-1';
            div.innerHTML = `
                <x-input class="block w-full" type="text" name="beneficiaries[]" required />
                <button type="button" onclick="removeBeneficiary(this)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Remove
                </button>
            `;
            container.appendChild(div);
            beneficiaryCount++;
        }

        function removeBeneficiary(button) {
            button.parentElement.remove();
        }
    </script>
    @endpush
</x-app-layout> 