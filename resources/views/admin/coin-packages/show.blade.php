@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Package #{{ $package->id }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('voyager.coin-packages.index') }}" class="btn btn-outline-secondary">Back</a>
                <a href="{{ route('voyager.coin-packages.edit', $package->id) }}" class="btn btn-primary">Edit</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header fw-semibold">Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Title</div>
                                <div class="fw-semibold">{{ $package->title }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small mb-1">Coins</div>
                                <div class="fw-semibold">{{ number_format($package->coins) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small mb-1">Sort Order</div>
                                <div>{{ $package->sort_order }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Price</div>
                                <div class="text-muted">{{ number_format($package->price, 2) }} {{ $package->currency }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Discount %</div>
                                @if($package->discount_percent)
                                    <span class="badge bg-info">{{ $package->discount_percent }}%</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Final Price</div>
                                <div class="fw-semibold">{{ number_format($package->final_price, 2) }} {{ $package->currency }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Currency</div>
                                <div>{{ $package->currency }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Active</div>
                                <span class="badge bg-{{ $package->is_active ? 'success' : 'secondary' }}">{{ $package->is_active ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Created At</div>
                                <div>{{ $package->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Updated At</div>
                                <div>{{ $package->updated_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- (Optional) Quick stats placeholder for future enhancements --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header fw-semibold">Quick Actions</div>
                    <div class="card-body">
                        <a href="{{ route('voyager.coin_packages.edit', $package->id) }}"
                           class="btn btn-primary w-100 mb-2">Edit Package</a>
                        <form action="{{ route('voyager.coin_packages.destroy', $package->id) }}" method="post"
                              onsubmit="return confirm('Delete this package?')" class="d-grid">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header fw-semibold">Meta</div>
                    <div class="card-body small text-muted">
                        <div>ID: {{ $package->id }}</div>
                        <div>Slug (Voyager): coin-packages</div>
                        <div>Model: App\Models\CoinPackage</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<?php
