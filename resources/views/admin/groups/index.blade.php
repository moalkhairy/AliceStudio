@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Groups</h4>
            <a href="{{ route('voyager.groups.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Group</a>
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
                    <label class="form-label">Studio</label>
                    <select name="studio_id" class="form-select">
                        <option value="">All</option>
                        @foreach($studios as $st)
                            <option value="{{ $st->id }}" @selected(request('studio_id')==$st->id)>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Name or Code">
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
                        <th>Studio</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Exclusive</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($groups as $g)
                        <tr>
                            <td>{{ $g->id }}</td>
                            <td>{{ $g->order }}</td>
                            <td>{{ $g->studio?->name }}</td>
                            <td>{{ $g->code }}</td>
                            <td><a href="{{ route('voyager.groups.show',$g->id) }}">{{ $g->name }}</a></td>
                            <td>
                                <span class="badge bg-{{ $g->exclusive_sections ? 'warning' : 'secondary' }}">{{ $g->exclusive_sections ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $g->is_active ? 'success':'secondary' }}">{{ $g->is_active ? 'Yes':'No' }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('voyager.groups.edit',$g->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('voyager.groups.destroy',$g->id) }}" method="post"
                                      class="d-inline" onsubmit="return confirm('Delete this group?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No groups yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">{{ $groups->links() }}</div>
        </div>
    </div>
@endsection
