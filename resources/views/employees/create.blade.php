<x-app-layout>
    <x-slot name="header">Add Employee</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Employees
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">New Employee</h3>
                <p class="mt-1 text-sm text-gray-500">Add a new team member</p>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Personal Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="ktp_number" class="block text-sm font-medium text-gray-700">KTP Number</label>
                            <input type="text" name="ktp_number" id="ktp_number" value="{{ old('ktp_number') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="ktp_status" class="block text-sm font-medium text-gray-700">KTP Status *</label>
                            <select name="ktp_status" id="ktp_status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                @foreach($ktpStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('ktp_status', 'not_submitted') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="npwp" class="block text-sm font-medium text-gray-700">NPWP</label>
                            <input type="text" name="npwp" id="npwp" value="{{ old('npwp') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Employment Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Position *</label>
                            <input type="text" name="position" id="position" value="{{ old('position') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="start_work_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                            <input type="date" name="start_work_date" id="start_work_date" value="{{ old('start_work_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div>
                            <label for="employee_type" class="block text-sm font-medium text-gray-700">Type *</label>
                            <select name="employee_type" id="employee_type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('employee_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="employee_status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="employee_status" id="employee_status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('employee_status', 'active') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary (IDR) *</label>
                            <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" step="1000" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Create Employee</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
