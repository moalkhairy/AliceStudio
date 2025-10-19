@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Add Choice</h4>
            <a href="{{ route('voyager.choices.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.choices.store') }}" method="post" class="card p-3">
            @csrf
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select">
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" @selected(old('section_id', request('section_id'))==$sec->id)>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gender Scope</label>
                    <select name="gender_scope" class="form-select">
                        @foreach(['male'=>'Male','female'=>'Female','both'=>'Both'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('gender_scope','both')==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Label</label>
                    <input name="label" value="{{ old('label') }}" class="form-control" placeholder="Display label">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input name="slug" value="{{ old('slug') }}" class="form-control" placeholder="unique_key">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Token</label>
                    <input name="token" value="{{ old('token') }}" class="form-control"
                           placeholder="Prompt token (optional)">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Negative Token</label>
                    <input name="negative_token" value="{{ old('negative_token') }}" class="form-control"
                           placeholder="Optional">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Weight</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',0) }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Icon URL</label>
                    <input name="icon_url" value="{{ old('icon_url') }}" class="form-control" placeholder="https://...">
                </div>

                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1"
                               {{ old('is_default') ? 'checked' : '' }} id="is_default">
                        <label class="form-check-label" for="is_default">Default</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',1) ? 'checked' : '' }} id="is_active">
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
@endsection
