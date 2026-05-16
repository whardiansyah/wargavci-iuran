@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- User Information Card -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Informasi Pengguna') }}</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Nama') }}</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                    </div>

                    <!-- Email Verification Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status Verifikasi Email') }}</label>
                        <div class="mt-1">
                            @if ($user->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ __('Terverifikasi') }} - {{ $user->email_verified_at->format('d M Y H:i') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    {{ __('Belum Terverifikasi') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Member Since -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Bergabung Sejak') }}</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Roles Card -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ __('Peran (Roles)') }}</h3>
                </div>

                @if ($roles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-lg text-gray-900">{{ $role->display_name ?? $role->name }}</h4>
                                    @if ($role->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $role->description }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ms-2 shrink-0">
                                    {{ strtoupper($role->name) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-50 p-4">
                        <p class="text-sm text-yellow-700">{{ __('Pengguna ini belum memiliki peran yang ditugaskan.') }}</p>
                    </div>
                @endif
            </div>

            <!-- Permissions Card -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ __('Izin (Permissions)') }}</h3>
                </div>

                @if ($permissions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($permissions as $permission)
                            <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-green-300 transition">
                                <svg class="h-5 w-5 text-green-500 mt-0.5 me-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $permission->display_name ?? $permission->name }}</p>
                                    @if ($permission->description)
                                        <p class="text-xs text-gray-600 mt-1">{{ $permission->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-50 p-4">
                        <p class="text-sm text-yellow-700">{{ __('Pengguna ini tidak memiliki izin apapun.') }}</p>
                    </div>
                @endif
            </div>

            <!-- Edit Profile Button -->
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Profil') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection