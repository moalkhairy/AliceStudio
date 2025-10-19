@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Step #{{ $step->id }}</h4>
            <a href="{{ route('voyager.steps.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.steps.update',$step->id) }}" method="post" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}" @selected(old('studio_id',$step->studio_id)==$st->id)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Code</label>
                    <input name="code" value="{{ old('code',$step->code) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',$step->order) }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input name="title" value="{{ old('title',$step->title) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Subtitle</label>
                    <input name="subtitle" value="{{ old('subtitle',$step->subtitle) }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Icon</label>
                    <input name="icon" value="{{ old('icon',$step->icon) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tip Text</label>
                    <input name="tip_text" value="{{ old('tip_text',$step->tip_text) }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',$step->is_active) ? 'checked':'' }} id="active">
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
