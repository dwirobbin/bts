@props(['id', 'name', 'placeholder' => '', 'value'])

<input type="date" id="{{ $id }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
    placeholder="{{ $placeholder }}..." value="{{ $value }}" />
