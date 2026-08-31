<x-app-layout>
    <x-slot name="header">Clients</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            Client Management
        </h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Clients</li>
            </ol>
        </nav>
    </div>

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-4">
            <form action="{{ route('clients.index') }}" method="GET" class="flex flex-1 max-w-md gap-2">
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search clients..."
                           class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-4 pr-10 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('clients.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-600 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Client
        </a>
    </div>

    <!-- Clients Table -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Company</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Contact</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Email</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Revenue</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Invoices</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">
                                    {{ strtoupper(substr($client->company_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-800">{{ $client->company_name }}</h5>
                                    @if($client->assignedEmployee)
                                    <p class="text-xs text-gray-400">Assigned: {{ $client->assignedEmployee->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $client->contact_name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $client->email }}</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-800">
                            Rp {{ number_format($client->total_revenue_idr, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-600">
                                {{ $client->invoices_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('clients.show', $client) }}" 
                                   class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-500" title="View">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" 
                                   class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-amber-500" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this client?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-500" title="Delete">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                </div>
                                <h3 class="mb-1 text-sm font-medium text-gray-800">No clients found</h3>
                                <p class="mb-4 text-sm text-gray-500">Get started by creating a new client.</p>
                                <a href="{{ route('clients.create') }}" 
                                   class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Client
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($clients->hasPages())
        <div class="border-t border-gray-200 px-4 py-4">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
