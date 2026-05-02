@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-teal-50 rounded-full mb-3">
            <i class="fas fa-key text-2xl text-teal-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Forgot Password?</h2>
        <p class="text-gray-500 text-sm mt-1">Enter your email and we'll send you a reset link</p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-300 rounded-lg px-4 py-3 space-y-1">
            @foreach($errors->all() as $error)
                <p class="text-red-700 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i> {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="you@example.com"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('email') border-red-400 @enderror">
            </div>
        </div>

        <button type="submit"
            class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i> Send Reset Link
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Remembered your password?
        <a href="{{ route('login') }}" class="text-teal-700 font-semibold hover:underline">Back to Login</a>
    </p>
@endsection
