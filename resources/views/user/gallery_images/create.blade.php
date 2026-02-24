@extends('user.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/cropper.css') }}">
@endsection
@section('content')

<div class="card">
    <div class="card-header">Add Gallery Image</div>

    <div class="card-body">
        <form action="{{ route('user.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

           <div class="form-group mb-3">
    <label>Item</label>
    <select name="item_id" class="form-control">
        @foreach($items as $item)
            @foreach($item->itemContents as $content)
                <option value="{{ $content->id }}">{{ $content->title }}</option>
            @endforeach
        @endforeach
    </select>
</div>


            <div class="form-group mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
