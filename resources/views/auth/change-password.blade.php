@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-key text-teal-700"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Change Password</h2>
                    <p class="text-gray-500 text-sm">Update your account password</p>
                </div>
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

            <form action="{{ route('change-password.update') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Current Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </span>
                        <input type="password" name="current_password" id="current_password" required
                            placeholder="Enter current password"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('current_password') border-red-400 @enderror">
                        <button type="button" onclick="togglePassword('current_password', 'eye-current')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i id="eye-current" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- New Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="Minimum 6 characters"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500 @error('password') border-red-400 @enderror">
                        <button type="button" onclick="togglePassword('password', 'eye-new')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i id="eye-new" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            placeholder="Repeat new password"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm transition focus:border-teal-500">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i id="eye-confirm" class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-teal-700 hover:bg-teal-800 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-lg transition duration-200 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
