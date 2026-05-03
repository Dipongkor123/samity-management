<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Samity Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .sm-toast-popup { padding:0 !important; border-radius:10px !important; box-shadow:0 8px 28px rgba(15,23,42,0.13),0 1px 4px rgba(0,0,0,0.06) !important; border:1px solid #e8edf3 !important; background:#fff !important; overflow:hidden !important; max-width:340px !important; font-family:'Segoe UI',system-ui,sans-serif !important; }
        .sm-toast-popup .swal2-html-container { margin:0 !important; padding:0 !important; overflow:visible !important; }
        .sm-toast-popup .swal2-timer-progress-bar { height:2px !important; }
        .sm-toast-inner { display:flex; align-items:center; gap:10px; padding:11px 12px; }
        .sm-toast-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px; }
        .sm-toast-msg { flex:1; font-size:0.815rem; font-weight:500; color:#1e293b; line-height:1.4; }
        .sm-toast-close { width:24px; height:24px; border-radius:6px; border:none; background:transparent; color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; transition:background 0.15s,color 0.15s; padding:0; }
        .sm-toast-close:hover { background:#f1f5f9; color:#475569; }
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
            {{-- Flash Messages via SweetAlert2 --}}

            @yield('content')
        </div>

        <p class="text-center text-teal-200 text-xs mt-6">
            &copy; {{ date('Y') }} Samity Management. All rights reserved.
        </p>
    </div>

    <script>
    function _toast(type, msg, ms) {
        const t = {
            success: { bg:'#dcfce7', color:'#16a34a', fa:'fa-check'      },
            error:   { bg:'#fee2e2', color:'#dc2626', fa:'fa-xmark'       },
            warning: { bg:'#fef9c3', color:'#ca8a04', fa:'fa-exclamation' },
        }[type] || { bg:'#dcfce7', color:'#16a34a', fa:'fa-check' };
        Swal.fire({
            html: `<div class="sm-toast-inner">
                     <span class="sm-toast-icon" style="background:${t.bg};color:${t.color};"><i class="fas ${t.fa}"></i></span>
                     <span class="sm-toast-msg">${msg}</span>
                     <button class="sm-toast-close" onclick="Swal.close()"><i class="fas fa-xmark"></i></button>
                   </div>`,
            toast: true, position: 'top-end',
            showConfirmButton: false, showCloseButton: false,
            timer: ms || 3500, timerProgressBar: true,
            customClass: { popup: 'sm-toast-popup' },
            didOpen: (el) => { el.addEventListener('mouseenter', Swal.stopTimer); el.addEventListener('mouseleave', Swal.resumeTimer); }
        });
    }
    @if(session('success')) _toast('success','{{ addslashes(session("success")) }}',3500); @endif
    @if(session('error'))   _toast('error',  '{{ addslashes(session("error")) }}',  4500); @endif

    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (!field || !icon) return;
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>
    @stack('scripts')
</body>
</html>
