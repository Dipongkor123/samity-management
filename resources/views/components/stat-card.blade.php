<div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; box-shadow:0 1px 6px rgba(0,0,0,0.05); display:flex; align-items:center; justify-content:space-between; gap:12px; transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 6px rgba(0,0,0,0.05)'">
    <div>
        <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94a3b8; margin:0 0 6px;">{{ $label }}</p>
        <p style="font-size:1.55rem; font-weight:800; color:#0f172a; margin:0; line-height:1;">{{ $value }}</p>
        @isset($sub)<p style="font-size:0.73rem; color:#94a3b8; margin:4px 0 0;">{{ $sub }}</p>@endisset
    </div>
    <div style="width:46px; height:46px; border-radius:12px; background:{{ $bg ?? '#f0fdfa' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <i class="{{ $icon }}" style="font-size:1.1rem; color:{{ $iconColor ?? '#0d9488' }};"></i>
    </div>
</div>
