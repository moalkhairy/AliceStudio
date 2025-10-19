@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Group #{{ $group->id }}</h4>
            <a href="{{ route('voyager.groups.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.groups.update',$group->id) }}" method="post" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}" @selected(old('studio_id',$group->studio_id)==$st->id)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Code</label>
                    <input name="code" value="{{ old('code',$group->code) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name',$group->name) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3">{{ old('description',$group->description) }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',$group->order) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Exclusive Sections</label>
                    <select name="exclusive_sections" class="form-select">
                        <option value="0" @selected(old('exclusive_sections',$group->exclusive_sections)==0)>No</option>
                        <option value="1" @selected(old('exclusive_sections',$group->exclusive_sections)==1)>Yes
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',$group->is_active) ? 'checked':'' }} id="active">
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
@endsection
