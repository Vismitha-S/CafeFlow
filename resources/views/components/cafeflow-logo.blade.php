{{-- CafeFlow brand logo SVG - used in navbar, footer, and auth pages --}}
<svg {{ $attributes->merge(['class' => 'h-8 w-auto']) }} viewBox="0 0 240 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    {{-- Coffee cup icon --}}
    <rect x="4" y="16" width="28" height="24" rx="4" fill="#3C2415"/>
    <rect x="6" y="18" width="24" height="20" rx="3" fill="#5A3E22"/>
    <path d="M32 22h4a6 6 0 0 1 0 12h-4" stroke="#3C2415" stroke-width="2.5" stroke-linecap="round"/>
    {{-- Steam wisps --}}
    <path d="M12 14c0-3 2-5 0-8" stroke="#E8722A" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
    <path d="M18 12c0-3 2-5 0-8" stroke="#E8722A" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
    <path d="M24 14c0-3 2-5 0-8" stroke="#E8722A" stroke-width="2" stroke-linecap="round" opacity="0.4"/>
    {{-- CafeFlow wordmark --}}
    <text x="50" y="35" font-family="Figtree, sans-serif" font-size="26" font-weight="700" fill="#3C2415">
        Cafe<tspan fill="#E8722A">Flow</tspan>
    </text>
</svg>
