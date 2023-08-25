@props(['id', 'name', 'emptyOptTxt'])

<select id="{{ $id }}" name="{{ $name }}" class="custom-select @error($name) is-invalid @enderror">
    <option value="" selected>{{ $emptyOptTxt }}</option>
    {{ $slot }}
</select>
