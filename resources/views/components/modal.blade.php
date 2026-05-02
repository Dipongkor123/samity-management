{{-- Usage: <x-modal id="modal-create" title="Add Samity"> ... form ... </x-modal> --}}
<div id="{{ $id }}" class="sm-modal" style="display:none; position:fixed; inset:0; z-index:200; background:rgba(15,23,42,0.6); align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(3px);">
    <div style="background:#fff; border-radius:16px; width:{{ $width ?? '560px' }}; max-width:100%; max-height:92vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3); display:flex; flex-direction:column;">
        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid #f1f5f9; flex-shrink:0;">
            <h3 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:8px;">
                @isset($icon)<i class="{{ $icon }}" style="color:#0d9488;"></i>@endisset
                {{ $title }}
            </h3>
            <button onclick="closeModal('{{ $id }}')" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:20px; line-height:1; padding:2px 6px; border-radius:6px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">&times;</button>
        </div>
        {{-- Body --}}
        <div style="padding:24px; flex:1; overflow-y:auto;">
            {{ $slot }}
        </div>
    </div>
</div>
