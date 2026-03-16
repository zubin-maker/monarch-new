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

                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="form-control p-3 rounded-3" placeholder="Your full name">
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="form-control p-3 rounded-3" placeholder="you@example.com">
                        </div>

                        {{-- Mobile --}}
                        <div class="mb-3">
                            <label class="form-label">Mobile *</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required
                                   class="form-control p-3 rounded-3" placeholder="+91 98765 43210">
                        </div>

                        {{-- Company --}}
                        <div class="mb-3">
                            <label class="form-label">Company *</label>
                            <input type="text" name="company" value="{{ old('company') }}" required
                                   class="form-control p-3 rounded-3" placeholder="Company name">
                        </div>

                        {{-- City --}}
                        <div class="mb-3">
                            <label class="form-label">City *</label>
                            <select name="city" class="form-select p-3 rounded-3 no-nice-select" required>
                                <option value="">Select City</option>
                                <option value="Mumbai" {{ old('city') == 'Mumbai' ? 'selected' : '' }}>Mumbai</option>
                                <option value="Delhi" {{ old('city') == 'Delhi' ? 'selected' : '' }}>Delhi</option>
                                <option value="Bengaluru" {{ old('city') == 'Bengaluru' ? 'selected' : '' }}>Bengaluru</option>
                                <option value="Hyderabad" {{ old('city') == 'Hyderabad' ? 'selected' : '' }}>Hyderabad</option>
                                <option value="Chennai" {{ old('city') == 'Chennai' ? 'selected' : '' }}>Chennai</option>
                                <option value="Other" {{ old('city') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Comment --}}
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" rows="3"
                                      class="form-control p-3 rounded-3"
                                      placeholder="Tell us a bit about your requirement (workspace size, timelines, etc.)">{{ old('comment') }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 rounded-3">
                                Send
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

    /* hide original browser arrow when Nice Select is active, but keep plain select for .no-nice-select */
    .nice-select + select.no-nice-select {
        display: none !important;
    }
</style>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Destroy Nice Select for the city dropdown so it looks like a normal select
    $('select.no-nice-select').niceSelect('destroy');
});
</script>

@endsection
