<div style="background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:16px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.04); display:flex; align-items:center; justify-content:space-between; gap:12px; transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 3px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,0.04)'">
    <div>
        <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#94a3b8; margin:0 0 5px;">{{ $label }}</p>
        <p style="font-size:1.25rem; font-weight:600; color:#1e293b; margin:0; line-height:1.2;">{{ $value }}</p>
        @isset($sub)<p style="font-size:0.72rem; color:#94a3b8; margin:4px 0 0;">{{ $sub }}</p>@endisset
    </div>
    <div style="width:40px; height:40px; border-radius:10px; background:{{ $bg ?? '#f0fdfa' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <i class="{{ $icon }}" style="font-size:1rem; color:{{ $iconColor ?? '#0d9488' }};"></i>
    </div>
</div>
