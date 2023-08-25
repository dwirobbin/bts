@props(['type' => 'text', 'placeholder' => '', 'id' => '', 'name' => '', 'value' => '', 'disabled' => false])

<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
    placeholder="{{ $placeholder }}..." value="{{ $value }}" @disabled($disabled) />
