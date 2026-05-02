<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Samity Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f766e 50%, #065f46 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full shadow-lg mb-3">
                <i class="fas fa-landmark text-3xl text-teal-700"></i>
            </div>
            <h1 class="text-white text-2xl font-bold tracking-wide">Samity Management</h1>
            <p class="text-teal-200 text-sm mt-1">Cooperative Society Platform</p>
        </div>

        {{-- Card --}}
        <div class="glass-card rounded-2xl p-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        <p class="text-center text-teal-200 text-xs mt-6">
            &copy; {{ date('Y') }} Samity Management. All rights reserved.
        </p>
    </div>

</body>
</html>
