@extends('user-front.layout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900">
                Contact for Corporate / Bulk Booking
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Fill the details below and our team will get back to you within 24 hours
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8 lg:p-12">
            <div class="container py-5">
                <div class="form-wrapper mx-auto col-lg-8">

                    <h2 class="fw-bold text-center mb-4">Submit Your Inquiry</h2>

                    <form method="POST" action="{{ route('bulk-inquiry.store') }}">
                        @csrf

                        {{-- Phone + Email --}}
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       class="form-control p-3 rounded-3" placeholder="+91 98765 43210">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="form-control p-3 rounded-3" placeholder="corporate@company.com">
                            </div>
                        </div>

                        {{-- Products Section --}}
                        <div class="mt-5">
                            <h4 class="fw-bold mb-3">Products You're Interested In</h4>

                            <div id="products-container">

                                {{-- First Product Row --}}

                                            <div class="product-row row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <select name="category[]" class="form-select p-3 rounded-3 category-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="product[]" class="form-select p-3 rounded-3" required>
                        <option value="">Select Product</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ \Illuminate\Support\Str::limit($item->title, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantity[]" min="1" placeholder="Qty"
                           class="form-control p-3 rounded-3 text-center" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn remove-row text-danger fs-3 lh-1">&times;</button>
                </div>
            </div>

                            </div>

                            <button type="button" id="add-product" class="btn btn-link text-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Add another product
                            </button>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 rounded-3">
                                Submit Inquiry
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Simple CSS Fix --}}
<style>
    select.form-select:focus {
        outline: none !important;
        box-shadow: none !important;
    }
</style>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Add new product row
    $('#add-product').click(function() {
        let productRow = `
            <div class="product-row row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <select name="category[]" class="form-select p-3 rounded-3 category-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="product[]" class="form-select p-3 rounded-3" required>
                        <option value="">Select Product</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ \Illuminate\Support\Str::limit($item->title, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantity[]" min="1" placeholder="Qty"
                           class="form-control p-3 rounded-3 text-center" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn remove-row text-danger fs-3 lh-1">&times;</button>
                </div>
            </div>
        `;
        $('#products-container').append(productRow);
    });

    // Remove product row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.product-row').remove();
    });

});
</script>

@endsection
