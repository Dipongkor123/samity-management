@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ __('Welcome Back') }}</h2>
    <p class="text-gray-500 text-sm mb-6">{{ __('Sign in to your account to continue') }}</p>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-300 rounded-lg px-4 py-3">
            @foreach($errors->all() as $error)
                <p class="text-red-700 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i> {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="you@example.com"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('email') border-red-400 @enderror">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                <a href="{{ route('password.request') }}" class="text-xs text-teal-600 hover:text-teal-800 hover:underline">
                    {{ __('Forgot password?') }}
                </a>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </span>
                <input type="password" name="password" id="password" required
                    placeholder="Enter your password"
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('password') border-red-400 @enderror">
                <button type="button" onclick="togglePassword('password', 'eye-login')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i id="eye-login" class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember"
                class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
            <label for="remember" class="text-sm text-gray-600 cursor-pointer">{{ __('Remember me') }}</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-sign-in-alt"></i> {{ __('Sign In') }}
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="text-teal-700 font-semibold hover:underline">{{ __('Create Account') }}</a>
    </p>
@endsection

