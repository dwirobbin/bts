@props(['type' => 'button', 'id' => '', 'dataId' => null, 'dataDelete' => null, 'dataToggle' => null, 'dataTarget' => null])

<button type="{{ $type }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'btn btn-sm']) }} data-id="{{ $dataId }}"
    data-delete="{{ $dataDelete }}" data-toggle="{{ $dataToggle }}" data-target="{{ $dataTarget }}">
    {{ $slot }}
</button>
