@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Sections</h4>
            <a href="{{ route('voyager.sections.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Section
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
                <div class="col-md-3">
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        <option value="">All</option>
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}" @selected(request('studio_id')==$st->id)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Group</label>
                    <select name="group_id" class="form-select">
                        <option value="">All</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" @selected(request('group_id')==$g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gender</label>
                    <select name="gender_scope" class="form-select">
                        <option value="">All</option>
                        @foreach(['male'=>'Male','female'=>'Female','both'=>'Both'] as $k=>$v)
                            <option value="{{ $k }}" @selected(request('gender_scope')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Name or Code">
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
                        <th>Order</th>
                        <th>Studio</th>
                        <th>Group</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Gender</th>
                        <th>General</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sections as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->order }}</td>
                            <td>{{ $s->studio?->name }}</td>
                            <td>{{ $s->group?->name ?? '—' }}</td>
                            <td>{{ $s->code }}</td>
                            <td><a href="{{ route('voyager.sections.show',$s->id) }}">{{ $s->name }}</a></td>
                            <td>{{ $s->input_type }}</td>
                            <td>{{ $s->selection_mode }}</td>
                            <td>{{ ucfirst($s->gender_scope) }}</td>
                            <td>
                                <span class="badge bg-{{ $s->is_general ? 'info' : 'secondary' }}">{{ $s->is_general ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="text-nowrap text-end">
                                <a href="{{ route('voyager.sections.edit',$s->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.sections.destroy',$s->id) }}" method="post"
                                      class="d-inline" onsubmit="return confirm('Delete this section?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">No sections yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $sections->links() }}
            </div>
        </div>
    </div>
@endsection
