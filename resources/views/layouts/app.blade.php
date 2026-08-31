<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Aksara Virtual') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* TailAdmin Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Sidebar styles - Clean white theme */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.375rem;
            color: #374151;
            transition: all 0.3s ease;
        }
        
        .sidebar-link:hover {
            background: #f3f4f6;
            color: #0d3d30;
        }
        
        .sidebar-link.active {
            background: rgba(13, 61, 48, 0.1);
            color: #0d3d30;
        }
        
        .sidebar-icon {
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
        }
        
        .menu-title {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            padding: 0.75rem 1rem 0.5rem;
            margin-top: 1rem;
        }
        
        /* Logo container styling */
        .logo-container {
            border-bottom: 1px solid #e5e7eb;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .logo-container:hover {
            background: #f9fafb;
        }
        
        .logo-img {
            transform: scale(1.4);
            transform-origin: center center;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"
             @click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="sidebar fixed left-0 top-0 z-50 flex h-screen w-72 flex-col overflow-y-auto duration-300 ease-in-out lg:static lg:translate-x-0">
            
            <!-- Logo -->
            <div class="logo-container flex items-center justify-center">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full">
                    <img src="{{ asset('images/aksara.png') }}" alt="Aksara Virtual" class="logo-img h-16 w-auto">
                </a> 
                <button class="block lg:hidden absolute right-4 top-5" @click="sidebarOpen = false">
                    <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <div class="flex flex-col overflow-y-auto duration-300 ease-linear px-4 py-4">
                <nav>
                    <div>
                        <h3 class="menu-title">Menu</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('dashboard') }}" 
                                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                        </ul>
                    </div>

                    @can('manage-clients')
                    <div>
                        <h3 class="menu-title">Business</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('clients.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                    Clients
                                </a>
                            </li>
                            @endcan
                            @can('manage-invoices')
                            <li>
                                <a href="{{ route('invoices.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Invoices
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    @can('manage-finance')
                    <div>
                        <h3 class="menu-title">Finance</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('finance.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('finance.index') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                    Overview
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('incomes.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Income
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('expenses.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                    Expenses
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('expense-categories.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                    </svg>
                                    Categories
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    @can('manage-employees')
                    <div>
                        <h3 class="menu-title">Human Resources</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('employees.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Employees
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('payslips.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('payslips.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    Payslips
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    @can('manage-finance')
                    <div>
                        <h3 class="menu-title">Reports</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('reports.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    Financial Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan

                    @can('manage-settings')
                    <div>
                        <h3 class="menu-title">Settings</h3>
                        <ul class="flex flex-col gap-1">
                            <li>
                                <a href="{{ route('users.index') }}" 
                                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Users
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.edit') }}" 
                                   class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                    <svg class="sidebar-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endcan
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            
            <!-- Header -->
            <header class="sticky top-0 z-30 flex w-full bg-white shadow-sm">
                <div class="flex flex-grow items-center justify-between px-4 py-4 md:px-6 2xl:px-11">
                    <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                        <!-- Hamburger Menu -->
                        <button @click="sidebarOpen = true"
                                class="z-50 block rounded-sm border border-gray-200 bg-white p-1.5 shadow-sm lg:hidden">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Page Title -->
                    <div class="hidden sm:block">
                        @isset($header)
                            <h1 class="text-xl font-semibold text-gray-800">{{ $header }}</h1>
                        @endisset
                    </div>

                    <!-- Header Right -->
                    <div class="flex items-center gap-3 2xl:gap-7">
                        <!-- User Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-4">
                                    <span class="hidden text-right lg:block">
                                        <span class="block text-sm font-medium text-gray-800">{{ Auth::user()->name }}</span>
                                        <span class="block text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</span>
                                    </span>
                                    <span class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </span>
                                    <svg class="hidden h-4 w-4 text-gray-400 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>

                                <x-dropdown-link :href="route('profile.edit')">
                                   
                                    My Profile
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                         
                                        Log Out
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="mb-6 flex w-full rounded-lg border-l-4 border-green-500 bg-green-50 px-4 py-3 shadow-md">
                            <div class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-green-500">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-sm font-medium text-green-800">Success</h5>
                                <p class="text-xs text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex w-full rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 shadow-md">
                            <div class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-red-500">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-sm font-medium text-red-800">Error</h5>
                                <p class="text-xs text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Validation errors (e.g. a failed save) don't flash to session('error') -
                         they land in $errors instead, and previously had no top-of-page summary,
                         so a failing field below the fold was easy to miss after the redirect
                         scrolled the page back to the top. --}}
                    @if ($errors->any())
                        <div class="mb-6 flex w-full rounded-lg border-l-4 border-red-500 bg-red-50 px-4 py-3 shadow-md">
                            <div class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-red-500">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h5 class="text-sm font-medium text-red-800">We couldn't save your changes</h5>
                                <p class="text-xs text-red-700">
                                    @php
                                        $errorFieldLabels = collect($errors->keys())
                                            ->map(function ($key) {
                                                $label = ucwords(str_replace('_', ' ', last(explode('.', $key))));
                                                return str_ireplace('Idr', 'IDR', $label);
                                            })
                                            ->unique()
                                            ->values();
                                    @endphp
                                    Please review the following field(s) and try again:
                                    <strong>{{ $errorFieldLabels->join(', ', ' and ') }}</strong>.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
