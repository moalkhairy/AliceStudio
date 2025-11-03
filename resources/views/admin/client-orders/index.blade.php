@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Client Orders</h4>
            <a href="{{ route('voyager.client_orders.index') }}" class="btn btn-outline-secondary">Refresh</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="get" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Client, Package, Payment Ref">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['paid','pending','failed'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" value="{{ request('currency') }}" class="form-control"
                           placeholder="AED">
                </div>

                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col-md-3">
                    <label class="form-label">Client ID</label>
                    <input type="number" name="client_id" value="{{ request('client_id') }}" class="form-control"
                           placeholder="e.g. 12">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Package ID</label>
                    <input type="number" name="package_id" value="{{ request('package_id') }}" class="form-control"
                           placeholder="e.g. 3">
                </div>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Package</th>
                        <th>Price</th>
                        <th>Discount %</th>
                        <th>Final</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Payment Ref</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td>{{ $o->id }}</td>
                            <td>
                                @if($o->client)
                                    <div class="fw-semibold">{{ $o->client->first_name }} {{ $o->client->last_name }}</div>
                                    <div class="text-muted small">{{ $o->client->email }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($o->package)
                                    <div class="fw-semibold">{{ $o->package->title }}</div>
                                    <div class="text-muted small">{{ number_format($o->package->coins) }} coins</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ number_format($o->price,2) }}</td>
                            <td>
                                @if($o->discount_percent)
                                    <span class="badge bg-info">{{ $o->discount_percent }}%</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ number_format($o->final_price,2) }}</td>
                            <td>{{ $o->currency }}</td>
                            <td>
                                @php
                                    $map = ['paid'=>'success','pending'=>'warning','failed'=>'danger'];
                                    $cls = $map[$o->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $cls }}">{{ ucfirst($o->status) }}</span>
                            </td>
                            <td class="text-muted">{{ $o->payment_ref ?: '—' }}</td>
                            <td class="text-muted">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('voyager.client_orders.show', $o->id) }}"
                                   class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No orders found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
