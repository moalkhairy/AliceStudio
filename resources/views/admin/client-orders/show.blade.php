@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Order #{{ $order->id }}</h4>
            <a href="{{ route('voyager.client_orders.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Summary</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Status</div>
                                @php
                                    $map = ['paid'=>'success','pending'=>'warning','failed'=>'danger'];
                                    $cls = $map[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $cls }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Payment Ref</div>
                                <div class="text-muted">{{ $order->payment_ref ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Created At</div>
                                <div class="text-muted">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Price</div>
                                <div class="text-muted">{{ number_format($order->price,2) }} {{ $order->currency }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Discount %</div>
                                @if($order->discount_percent)
                                    <span class="badge bg-info">{{ $order->discount_percent }}%</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Final Price</div>
                                <div class="fw-semibold">{{ number_format($order->final_price,2) }} {{ $order->currency }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($order->package)
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Package</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Title</div>
                                    <div class="fw-semibold">{{ $order->package->title }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Coins</div>
                                    <div class="fw-semibold">{{ number_format($order->package->coins) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Currency</div>
                                    <div>{{ $order->currency }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Client</div>
                    <div class="card-body">
                        @if($order->client)
                            <div class="fw-semibold">{{ $order->client->first_name }} {{ $order->client->last_name }}</div>
                            <div class="text-muted small">{{ $order->client->email }}</div>
                            <div class="text-muted small">ID: {{ $order->client->id }}</div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-semibold">Meta</div>
                    <div class="card-body small text-muted">
                        <div>Order ID: {{ $order->id }}</div>
                        <div>Package ID: {{ $order->coin_package_id }}</div>
                        <div>Status: {{ $order->status }}</div>
                        <div>Created: {{ $order->created_at?->format('Y-m-d H:i') }}</div>
                        <div>Updated: {{ $order->updated_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
