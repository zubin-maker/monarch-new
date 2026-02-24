@extends('user.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/cropper.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="card-header">Edit Gallery Image</div>

    <div class="card-body">
        <form action="{{ route('user.gallery.update', $image->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            

            <div class="form-group mb-3">
                <label>Item</label>
                <select name="item_id" id="item_id" class="form-control">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        @foreach($item->itemContents as $content)
                            <option value="{{ $content->id }}"
                                {{ $image->item_id == $content->id ? 'selected' : '' }}>
                                {{ $content->title }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label>Current Image</label><br>
                @if($image->image)
                    <img src="{{ asset('uploads/gallery/'.$image->image) }}" width="120" alt="Current Image">
                @endif
            </div>

            <div class="form-group mb-3">
                <label>Change Image</label>
                <input type="file" name="image" class="form-control">
                <small class="text-muted">Leave empty if you don't want to change the image.</small>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection

