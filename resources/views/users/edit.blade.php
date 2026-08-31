<x-app-layout>
    <x-slot name="header">Edit User</x-slot>

    @section('title', 'Edit User')

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-500">/</li>
            <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">Users</a></li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-700 font-medium">Edit</li>
        </ol>
    </nav>

    <div class="max-w-2xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
                <p class="text-sm text-gray-500 mt-1">Update user information</p>
            </div>
            
            <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Username hanya boleh mengandung huruf, angka, dash dan underscore</p>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-gray-400">(Optional)</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
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
                        <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="finance" {{ old('role', $user->role) === 'finance' ? 'selected' : '' }}>Finance</option>
                        <option value="hr" {{ old('role', $user->role) === 'hr' ? 'selected' : '' }}>HR</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-4">Leave password fields empty if you don't want to change the password</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Enter new password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
