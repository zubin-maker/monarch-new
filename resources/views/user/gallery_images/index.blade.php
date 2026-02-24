@extends('user.layout')
@includeIf('user.partials.rtl-style')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Gallery Images</h4>
    <a href="{{ route('user.gallery.create') }}" class="btn btn-primary">Add New</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($images as $img)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img src="{{ asset('uploads/gallery/'.$img->image) }}" width="80"></td>
                    
                        <td>
                            <a href="{{ route('user.gallery.edit', $img->id) }}" class="btn btn-sm btn-warning">Edit</a>

                         <form action="{{ route('user.gallery.destroy', $img->id) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
</form>


                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
@endsection
