@extends('user.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">{{ __('Bulk Order Details') }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{ route('admin.dashboard') }}">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow"></i></li>
        <li class="nav-item"><a href="{{ route('user.bulk-order') }}">{{ __('Bulk Orders') }}</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('Order Details') }}</h4>
            </div>
            <div class="card-body">
                <p><strong>{{ __('Phone') }}:</strong> {{ $order->phone }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ $order->email }}</p>
                <hr>
                <h5>{{ __('Products') }}</h5>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $categoryNames = $categories->pluck('name', 'id')->toArray();
                            $productTitles = $items->pluck('title', 'id')->toArray();

                            $categoriesIds = json_decode($order->category_id, true);
                            $productsIds   = json_decode($order->item_id, true);
                            $quantities    = json_decode($order->quantity, true);
                        @endphp

                        @foreach($productsIds as $index => $productId)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $categoryNames[$categoriesIds[$index]] ?? '-' }}</td>
                                <td>{{ $productTitles[$productId] ?? '-' }}</td>
                                <td>{{ $quantities[$index] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('user.bulk-order') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
