@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Edit Section #{{ $section->id }}</h4>
            <a href="{{ route('voyager.sections.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('voyager.sections.update', $section->id) }}" method="post" class="card p-3">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}" @selected(old('studio_id',$section->studio_id)==$st->id)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Group (optional)</label>
                    <select name="group_id" class="form-select">
                        <option value="">None</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected(old('group_id',$section->group_id)==$g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender Scope</label>
                    <select name="gender_scope" class="form-select">
                        @foreach(['male'=>'Male','female'=>'Female','both'=>'Both'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('gender_scope',$section->gender_scope)==$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Code</label>
                    <input name="code" value="{{ old('code',$section->code) }}" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name',$section->name) }}" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Help Text</label>
                    <input name="help_text" value="{{ old('help_text',$section->help_text) }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Input Type</label>
                    <select name="input_type" class="form-select">
                        @foreach(['chips','text','textarea','upload'] as $t)
                            <option value="{{ $t }}" @selected(old('input_type',$section->input_type)==$t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Selection Mode</label>
                    <select name="selection_mode" class="form-select">
                        @foreach(['single','multiple'] as $m)
                            <option value="{{ $m }}" @selected(old('selection_mode',$section->selection_mode)==$m)>{{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Min Select</label>
                    <input type="number" name="min_select" value="{{ old('min_select',$section->min_select) }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Max Select</label>
                    <input type="number" name="max_select" value="{{ old('max_select',$section->max_select) }}"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" value="{{ old('order',$section->order) }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Is General</label>
                    <select name="is_general" class="form-select">
                        <option value="0" @selected(old('is_general',$section->is_general)==0)>No</option>
                        <option value="1" @selected(old('is_general',$section->is_general)==1)>Yes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Required</label>
                    <select name="is_required" class="form-select">
                        <option value="0" @selected(old('is_required',$section->is_required)==0)>No</option>
                        <option value="1" @selected(old('is_required',$section->is_required)==1)>Yes</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               {{ old('is_active',$section->is_active) ? 'checked' : '' }} id="active">
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
