@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Coin Packages</h4>
            <a href="{{ route('voyager.coin_packages.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Package
            </a>
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
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select">
                        <option value="">All</option>
                        <option value="1" @selected(request('active')==='1')>Active</option>
                        <option value="0" @selected(request('active')==='0')>Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" value="{{ request('currency') }}" class="form-control"
                           placeholder="e.g. AED">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Title or Currency">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Coins</th>
                        <th>Price</th>
                        <th>Discount %</th>
                        <th>Final Price</th>
                        <th>Currency</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($packages as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->sort_order }}</td>
                            <td class="fw-semibold">{{ $p->title }}</td>
                            <td>{{ number_format($p->coins) }}</td>
                            <td class="text-muted">{{ number_format($p->price,2) }}</td>
                            <td>
                                @if($p->discount_percent)
                                    <span class="badge bg-info">{{ $p->discount_percent }}%</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ number_format($p->final_price,2) }}</td>
                            <td>{{ $p->currency }}</td>
                            <td>
                                <span class="badge bg-{{ $p->is_active ? 'success' : 'secondary' }}">
                                    {{ $p->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('voyager.coin_packages.edit',$p->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.coin_packages.destroy',$p->id) }}" method="post"
                                      class="d-inline" onsubmit="return confirm('Delete this package?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No packages found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $packages->links() }}
            </div>
        </div>
    </div>
@endsection
