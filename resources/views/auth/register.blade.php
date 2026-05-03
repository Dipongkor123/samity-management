@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Create Account</h2>
    <p class="text-gray-500 text-sm mb-6">Join the Samity Management platform</p>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-300 rounded-lg px-4 py-3 space-y-1">
            @foreach($errors->all() as $error)
                <p class="text-red-700 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i> {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                </span>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="Enter your full name"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('name') border-red-400 @enderror">
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="you@example.com"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('email') border-red-400 @enderror">
            </div>
        </div>

        {{-- Phone --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                </span>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    placeholder="+880 1XX-XXXXXXX"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('phone') border-red-400 @enderror">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </span>
                <input type="password" name="password" id="password" required
                    placeholder="Minimum 6 characters"
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('password') border-red-400 @enderror">
                <button type="button" onclick="togglePassword('password', 'eye-pass')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i id="eye-pass" class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </span>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    placeholder="Repeat your password"
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500">
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i id="eye-confirm" class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 mt-2">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-teal-700 font-semibold hover:underline">Sign In</a>
    </p>
@endsection

