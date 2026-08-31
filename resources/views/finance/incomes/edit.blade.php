<x-app-layout>
    <x-slot name="header">Edit Income</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('incomes.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Income
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Edit Income Entry</h3>
            </div>

            <form action="{{ route('incomes.update', $income) }}" method="POST" class="p-6 space-y-6">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <input type="text" name="description" id="description" value="{{ old('description', $income->description) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $income->amount) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency *</label>
                        <select name="currency" id="currency" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="IDR" {{ old('currency', $income->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="USD" {{ old('currency', $income->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="AUD" {{ old('currency', $income->currency) == 'AUD' ? 'selected' : '' }}>AUD</option>
                        </select>
                    </div>

                    <div>
                        <label for="income_date" class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" name="income_date" id="income_date" value="{{ old('income_date', $income->income_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                        <select name="client_id" id="client_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $income->client_id) == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('incomes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Update Income</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
