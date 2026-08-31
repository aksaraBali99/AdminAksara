<x-app-layout>
    <x-slot name="header">Edit Category</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('expense-categories.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Categories
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Edit Category</h3>
            </div>

            <form action="{{ route('expense-categories.update', $category) }}" method="POST" class="p-6 space-y-6">
                @csrf @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('expense-categories.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
