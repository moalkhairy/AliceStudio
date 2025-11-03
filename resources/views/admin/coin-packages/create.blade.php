@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Coin Package</h4>
            <a href="{{ route('voyager.coin_packages.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('voyager.coin_packages.store') }}" class="card p-3">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Coins <span class="text-danger">*</span></label>
                    <input type="number" name="coins" class="form-control" value="{{ old('coins') }}" min="1" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}"
                           min="0">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01"
                           min="0" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Discount %</label>
                    <input type="number" name="discount_percent" class="form-control"
                           value="{{ old('discount_percent') }}" min="0" max="100">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency','AED') }}"
                           maxlength="8">
                    <small class="text-muted">Default: AED</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label d-block">Active</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label">Enable package</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Final Price (auto)</label>
                    <input type="text" class="form-control" id="final_price_preview" value="—" disabled>
                    <small class="text-muted">Calculated from Price &amp; Discount by system.</small>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">
                    <i class="bx bx-check"></i> Save
                </button>
                <a href="{{ route('voyager.coin_packages.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('javascript')
    <script>
        (function () {
            const priceEl = document.querySelector('input[name="price"]');
            const discEl = document.querySelector('input[name="discount_percent"]');
            const outEl = document.getElementById('final_price_preview');

            function recalc() {
                const price = parseFloat(priceEl.value || 0);
                let disc = parseInt(discEl.value || 0, 10);
                if (isNaN(disc)) disc = 0;
                disc = Math.max(0, Math.min(100, disc));
                const final = price * (100 - disc) / 100;
                outEl.value = isFinite(final) ? final.toFixed(2) : '—';
            }

            ['input', 'change'].forEach(evt => {
                priceEl.addEventListener(evt, recalc);
                discEl.addEventListener(evt, recalc);
            });

            recalc();
        })();
    </script>
@endpush
