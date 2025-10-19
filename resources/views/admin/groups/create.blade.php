@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Group</h4>
            <a href="{{ route('voyager.groups.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.groups.store') }}" method="post" class="card p-3">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Code</label>
                    <input name="code" value="{{ old('code') }}" class="form-control" placeholder="model_style">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control" placeholder="Model Style">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',0) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Exclusive Sections</label>
                    <select name="exclusive_sections" class="form-select">
                        <option value="0" @selected(old('exclusive_sections',1)==0)>No</option>
                        <option value="1" @selected(old('exclusive_sections',1)==1)>Yes</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',1) ? 'checked':'' }} id="active">
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
