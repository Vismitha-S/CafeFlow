@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-cream-300/90 focus:border-accent-400 focus:ring-2 focus:ring-accent-400/20 rounded-xl shadow-xs text-sm text-coffee-900 placeholder-coffee-400 bg-white/90 transition-all duration-150']) !!}>
