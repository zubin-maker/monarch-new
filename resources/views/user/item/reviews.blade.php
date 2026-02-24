@extends('user.layout')

@section('content')

<div class="page-header">
    <h4 class="page-title">{{ __('Manage Reviews') }}</h4>
</div>

<div class="card">
    <div class="card-body">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= ADD MULTIPLE REVIEWS ================= --}}
        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="item_id" value="{{$item->id}}">

            <h5 class="mb-3">{{ __('Add Reviews') }}</h5>

            <div id="review-container">

                {{-- Default Review Box --}}
                <div class="row review-box border p-3 mb-3">
                    <div class="col-md-3">
                        <label>{{ __('Name') }} *</label>
                        <input type="text" name="name[]" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label>{{ __('Rating') }} *</label>
                        <select name="rating[]" class="form-control" required>
                            <option value="">{{ __('Select') }}</option>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }} Star</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label>{{ __('Description') }} *</label>
                        <textarea name="description[]" class="form-control" required></textarea>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-review">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

            </div>

            <button type="button" class="btn btn-success btn-sm mb-3" id="add-review">
                <i class="fas fa-plus"></i> {{ __('Add More') }}
            </button>

            <div>
                <button type="submit" class="btn btn-primary">
                    {{ __('Submit Reviews') }}
                </button>
            </div>
        </form>

        <hr>

        {{-- ================= EXISTING REVIEWS LIST ================= --}}
        <h5 class="mb-3">{{ __('Existing Reviews') }}</h5>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Rating') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th width="180">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>

                            {{-- Update Form --}}
                            <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <td>
                                    <input type="text"
                                           name="name"
                                           value="{{ $review->name }}"
                                           class="form-control" required>
                                </td>

                                <td>
                                    <select name="rating" class="form-control" required>
                                        @for($i=1; $i<=5; $i++)
                                            <option value="{{ $i }}"
                                                {{ $review->rating == $i ? 'selected' : '' }}>
                                                {{ $i }} Star
                                            </option>
                                        @endfor
                                    </select>
                                </td>

                                <td>
                                    <input type="text"
                                           name="description"
                                           value="{{ $review->description }}"
                                           class="form-control" required>
                                </td>

                                <td>
                                    <button class="btn btn-primary btn-sm">
                                        {{ __('Update') }}
                                    </button>
                            </form>

                            {{-- Delete Form --}}
                            <form action="{{ route('reviews.destroy', $review->id) }}"
                                  method="POST"
                                  style="display:inline-block;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this review?')">
                                    {{ __('Delete') }}
                                </button>
                            </form>

                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                {{ __('No Reviews Found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection


@section('vuescripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Add Review Box
    document.getElementById('add-review').addEventListener('click', function () {

        let container = document.getElementById('review-container');

        let html = `
        <div class="row review-box border p-3 mb-3">
            <div class="col-md-3">
                <label>Name *</label>
                <input type="text" name="name[]" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label>Rating *</label>
                <select name="rating[]" class="form-control" required>
                    <option value="">Select</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Star</option>
                    <option value="3">3 Star</option>
                    <option value="4">4 Star</option>
                    <option value="5">5 Star</option>
                </select>
            </div>

            <div class="col-md-5">
                <label>Description *</label>
                <textarea name="description[]" class="form-control" required></textarea>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-review">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
    });

    // Remove Review Box
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-review')) {
            e.target.closest('.review-box').remove();
        }
    });

});
</script>
@endsection