@props(['id', 'name', 'placeholder' => '', 'value'])

<input type="date" id="{{ $id }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
    placeholder="{{ $placeholder }}..." value="{{ $value }}" />

{{-- <input type="text" id="{{ $id }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
    placeholder="{{ $placeholder }}..." data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask value="{{ $value }}" /> --}}
