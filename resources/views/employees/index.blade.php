<x-app-layout>
    <x-slot name="header">Employees</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Employee Management</h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Employees</li>
            </ol>
        </nav>
    </div>

    <!-- Filters & Actions -->
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form action="{{ route('employees.index') }}" method="GET">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-1 flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Name, email, position..."
                               class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="w-full sm:w-auto min-w-[140px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Status</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                            <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto min-w-[140px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Type</label>
                        <select name="type" class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="full_time" {{ request('type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ request('type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="freelance" {{ request('type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Clear</a>
                    <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Employee
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Employee</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Position</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Type</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Salary</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-600 font-semibold text-sm">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-800">{{ $employee->name }}</h5>
                                    <p class="text-xs text-gray-400">{{ $employee->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $employee->position }}</td>
                        <td class="px-4 py-4">
                            @php
                                $typeColors = [
                                    'full_time' => 'bg-blue-100 text-blue-600',
                                    'part_time' => 'bg-amber-100 text-amber-600',
                                    'freelance' => 'bg-purple-100 text-purple-600',
                                ];
                                $typeLabels = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'freelance' => 'Freelance'];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeColors[$employee->employee_type] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $typeLabels[$employee->employee_type] ?? $employee->employee_type }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-600',
                                    'resigned' => 'bg-gray-100 text-gray-600',
                                    'terminated' => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColors[$employee->employee_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($employee->employee_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-800">{{ $employee->formatted_salary }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employees.show', $employee) }}" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-amber-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                </div>
                                <h3 class="mb-1 text-sm font-medium text-gray-800">No employees found</h3>
                                <p class="mb-4 text-sm text-gray-500">Add your first employee to get started.</p>
                                <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Employee
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($employees->hasPages())
        <div class="border-t border-gray-200 px-4 py-4">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
