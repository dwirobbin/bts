@props(['id', 'name', 'value', 'label', 'checked' => false])

<div class="custom-control custom-radio">
    <input type="radio" id="{{ $id }}" name="{{ $name }}" class="custom-control-input" value="{{ $value }}"
        @checked($checked) />
    <label for="{{ $id }}" class="custom-control-label">
        {{ $label }}
    </label>
</div>
