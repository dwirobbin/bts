@props(['for'])

<label for="{{ $for }}" {{ $attributes->merge(['class' => '']) }}>{{ $slot }}</label>
