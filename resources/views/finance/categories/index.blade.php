<x-app-layout>
    <x-slot name="header">Expense Categories</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Expense Categories</h2>
                <p class="mt-1 text-sm text-gray-500">Organize your expenses by category</p>
            </div>
            <a href="{{ route('expense-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-teal-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Category
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $category->expenses_count }} items
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('expense-categories.edit', $category) }}" class="text-gray-400 hover:text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($category->expenses_count == 0)
                                    <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No categories yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
