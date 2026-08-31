<x-app-layout>
    <x-slot name="header">Create User</x-slot>

    @section('title', 'Create User')

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-500">/</li>
            <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">Users</a></li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-700 font-medium">Create</li>
        </ol>
    </nav>

    <div class="max-w-2xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Create New User</h3>
                <p class="text-sm text-gray-500 mt-1">Add a new user to the system</p>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Enter unique username (letters, numbers, dash, underscore)">
                    <p class="mt-1 text-xs text-gray-500">Username hanya boleh mengandung huruf, angka, dash dan underscore</p>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-gray-400">(Optional)</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="user@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Role</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="finance" {{ old('role') === 'finance' ? 'selected' : '' }}>Finance</option>
                        <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
