@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Choices</h4>
            <a href="{{ route('voyager.choices.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Choice
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
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select">
                        <option value="">All</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" @selected(request('section_id')==$sec->id)>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Label, Slug, or Token">
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
                        <th>Section</th>
                        <th>Label</th>
                        <th>Slug</th>
                        <th>Gender</th>
                        <th>Default</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($choices as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->order }}</td>
                            <td>{{ $c->section?->name }}</td>
                            <td>{{ $c->label }}</td>
                            <td class="text-muted">{{ $c->slug }}</td>
                            <td>{{ ucfirst($c->gender_scope) }}</td>
                            <td>
                                <span class="badge bg-{{ $c->is_default ? 'info' : 'secondary' }}">{{ $c->is_default ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('voyager.choices.edit',$c->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.choices.destroy',$c->id) }}" method="post"
                                      class="d-inline" onsubmit="return confirm('Delete this choice?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No choices yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $choices->links() }}
            </div>
        </div>
    </div>
@endsection
