@if ($type == 'image')
  {{-- <button class="remove-img btn btn-sm btn-remove text-white btn-danger" type="button" data-url="{{ $url }}"
    data-name="{{ $name }}">
    <i class="fas fa-times"></i>
  </button> --}}
  <button class="remove-img btn btn-sm btn-remove btn-remove-sm text-white btn-danger" type="button"
    data-url="{{ $url }}" data-name="{{ $name }}">
    <i class="fas fa-times"></i>
  </button>
@else
  <button class="remove-img btn btn-sm btn-remove btn-remove-sm text-white btn-danger" type="button"
    data-url="{{ $url }}" data-name="{{ $name }}">
    <i class="fas fa-times"></i>
  </button>
@endif
