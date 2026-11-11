@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-theme-border focus:border-brand focus:ring-brand rounded-md shadow-sm']) }}>
