@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Studio: {{ $studio->name }}</h4>
            <a href="{{ route('voyager.studios.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2"><strong>Code:</strong> {{ $studio->code }}</div>
                        <div class="mb-2"><strong>Order:</strong> {{ $studio->order }}</div>
                        <div class="mb-2">
                            <strong>Active:</strong>
                            <span class="badge bg-{{ $studio->is_active ? 'success' : 'secondary' }}">
              {{ $studio->is_active ? 'Yes' : 'No' }}
            </span>
                        </div>
                        <div>
                            <strong>Description:</strong><br>
                            {{ $studio->description ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Sections</strong>
                        <a href="{{ route('voyager.sections.create') }}?studio_id={{ $studio->id }}"
                           class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Add Section
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Mode</th>
                                <th>Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($studio->sections as $sec)
                                <tr>
                                    <td>{{ $sec->order }}</td>
                                    <td>{{ $sec->code }}</td>
                                    <td><a href="{{ route('voyager.sections.show',$sec->id) }}">{{ $sec->name }}</a>
                                    </td>
                                    <td>{{ ucfirst($sec->input_type) }}</td>
                                    <td>{{ ucfirst($sec->selection_mode) }}</td>
                                    <td>
                    <span class="badge bg-{{ $sec->is_active ? 'success' : 'secondary' }}">
                      {{ $sec->is_active ? 'Yes' : 'No' }}
                    </span>
                                    </td>
                                    <td class="text-nowrap text-end">
                                        <a href="{{ route('voyager.sections.edit',$sec->id) }}"
                                           class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No sections yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
