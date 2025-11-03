@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Transaction #{{ $transaction->id }}</h4>
            <a href="{{ route('voyager.wallet_transactions.index') }}" class="btn btn-outline-secondary">Back</a>
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
                    <div class="card-header fw-semibold">Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Type</div>
                                <div class="fw-semibold">{{ str_replace('_',' ', $transaction->type) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Amount</div>
                                <div class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->amount }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Balance After</div>
                                <div class="fw-semibold">{{ $transaction->balance_after }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Created At</div>
                                <div class="text-muted">{{ $transaction->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Updated At</div>
                                <div class="text-muted">{{ $transaction->updated_at?->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($transaction->meta))
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Meta</div>
                        <div class="card-body">
                            <pre class="mb-0"
                                 style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($transaction->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header fw-semibold">Client</div>
                    <div class="card-body">
                        @if($transaction->client)
                            <div class="fw-semibold">{{ $transaction->client->first_name }} {{ $transaction->client->last_name }}</div>
                            <div class="text-muted small">{{ $transaction->client->email }}</div>
                            <div class="text-muted small">ID: {{ $transaction->client->id }}</div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header fw-semibold">Meta</div>
                    <div class="card-body small text-muted">
                        <div>ID: {{ $transaction->id }}</div>
                        <div>Slug (Voyager): wallet-transactions</div>
                        <div>Model: App\Models\WalletTransaction</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
