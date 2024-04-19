@props(['type' => 'date', 'id', 'name', 'class' => '', 'placeholder' => '', 'value'])

<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
    class="{{ $class }} form-control @error($name) is-invalid @enderror" placeholder="{{ $placeholder }}..." value="{{ $value }}" />
