@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Wallet Transactions</h4>
            <a href="{{ route('voyager.wallet_transactions.index') }}" class="btn btn-outline-secondary">Refresh</a>
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
                           placeholder="Type, Client name/email, ID">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        @foreach(['signup_bonus','package_purchase','image_generation','generation_refund','admin_adjustment'] as $t)
                            <option value="{{ $t }}" @selected(request('type')===$t)>{{ str_replace('_',' ', $t) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Sign</label>
                    <select name="sign" class="form-select">
                        <option value="">All</option>
                        <option value="credit" @selected(request('sign')==='credit')>Credit (+)</option>
                        <option value="debit" @selected(request('sign')==='debit')>Debit (−)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Client ID</label>
                    <input type="number" name="client_id" value="{{ request('client_id') }}" class="form-control"
                           placeholder="e.g. 12">
                </div>

                <div class="col-md-1">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="col-md-1">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                <div class="col-md-1">
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
                        <th>Client</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance After</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>
                                @if($t->client)
                                    <div class="fw-semibold">{{ $t->client->first_name }} {{ $t->client->last_name }}</div>
                                    <div class="text-muted small">{{ $t->client->email }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ str_replace('_',' ', $t->type) }}</td>
                            <td class="text-end {{ $t->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $t->amount >= 0 ? '+' : '' }}{{ $t->amount }}
                            </td>
                            <td class="text-end">{{ $t->balance_after }}</td>
                            <td class="text-muted">{{ $t->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('voyager.wallet_transactions.show', $t->id) }}"
                                   class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No transactions found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
