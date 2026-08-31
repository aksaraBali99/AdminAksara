<x-app-layout>
    <x-slot name="header">Edit Client</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Clients
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Edit Client</h3>
                <p class="mt-1 text-sm text-gray-500">Update client information</p>
            </div>

            <form action="{{ route('clients.update', $client) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $client->company_name) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name *</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $client->contact_name) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $client->phone_number) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $client->start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_employee_id" class="block text-sm font-medium text-gray-700">Assigned Employee</label>
                        <select name="assigned_employee_id" id="assigned_employee_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('assigned_employee_id', $client->assigned_employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->position }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('address', $client->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
