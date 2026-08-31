<x-app-layout>
    <x-slot name="header">Employee Details</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Employees
            </a>
            <div class="flex gap-2">
                <a href="{{ route('payslips.create', ['employee_id' => $employee->id]) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Payslip
                </a>
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Employee Info Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="text-center">
                            <div class="inline-flex h-20 w-20 bg-blue-100 rounded-full items-center justify-center">
                                <span class="text-blue-700 font-bold text-2xl">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                            </div>
                            <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $employee->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $employee->employee_id }}</p>
                        </div>

                        <div class="mt-6 flex justify-center gap-2">
                            @php
                                $statusColors = ['active' => 'bg-green-100 text-green-800', 'terminated' => 'bg-red-100 text-red-800', 'resigned' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$employee->employee_status] }}">
                                {{ $employee->status_label }}
                            </span>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $employee->type_label }}
                            </span>
                        </div>

                        <dl class="mt-6 space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                            </div>
                            @if($employee->address)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->address }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Start Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->start_work_date->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Base Salary</dt>
                                <dd class="mt-1 text-lg font-bold text-teal-600">{{ $employee->formatted_salary }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- KTP & Documents -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900">Documents</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">KTP Status</span>
                                @php
                                    $ktpColors = ['verified' => 'bg-green-100 text-green-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'not_submitted' => 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $ktpColors[$employee->ktp_status] }}">
                                    {{ $employee->ktp_status_label }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <select name="document_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                    @foreach($documentTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="file" name="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Upload Document</button>
                        </form>

                        @if($employee->documents->count() > 0)
                        <div class="mt-4 space-y-2">
                            @foreach($employee->documents as $doc)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-800">{{ $doc->type_label }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $doc->file_url }}" target="_blank" class="text-teal-600 hover:text-teal-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('employees.documents.destroy', [$employee, $doc]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payslips & Assigned Clients -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recent Payslips -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Payslips</h3>
                        <a href="{{ route('payslips.index', ['employee_id' => $employee->id]) }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payslip #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Salary</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employee->payslips as $payslip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payslip->payslip_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payslip->period_start->format('d M') }} - {{ $payslip->period_end->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-teal-600">{{ $payslip->formatted_net_salary }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('payslips.pdf', $payslip) }}" class="text-teal-600 hover:text-teal-800">PDF</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No payslips yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Clients -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Assigned Clients</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employee->assignedClients as $client)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('clients.show', $client) }}" class="text-sm font-medium text-gray-900 hover:text-teal-600">{{ $client->company_name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">Rp {{ number_format($client->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No assigned clients</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
