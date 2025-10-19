@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Studio</h4>
            <a href="{{ route('voyager.studios.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.studios.store') }}" method="post" class="card p-3">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Code</label>
                    <input name="code" value="{{ old('code') }}" class="form-control" placeholder="model">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" placeholder="Model Studio">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',0) }}" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control"
                              placeholder="Optional">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',1) ? 'checked' : '' }} id="active">
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
@endsection
