@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Group: {{ $group->name }}</h4>
            <a href="{{ route('voyager.groups.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2"><strong>Studio:</strong> {{ $group->studio?->name }}</div>
                        <div class="mb-2"><strong>Code:</strong> {{ $group->code }}</div>
                        <div class="mb-2"><strong>Exclusive:</strong> {{ $group->exclusive_sections ? 'Yes' : 'No' }}
                        </div>
                        <div class="mb-2"><strong>Order:</strong> {{ $group->order }}</div>
                        <div class="mb-2"><strong>Active:</strong> <span
                                    class="badge bg-{{ $group->is_active?'success':'secondary' }}">{{ $group->is_active?'Yes':'No' }}</span>
                        </div>
                        <div><strong>Description:</strong><br>{{ $group->description ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Sections in this Group</strong>
                        <a href="{{ route('voyager.sections.create') }}?group_id={{ $group->id }}&studio_id={{ $group->studio_id }}"
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
                                <th>Gender</th>
                                <th>Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($group->sections as $sec)
                                <tr>
                                    <td>{{ $sec->order }}</td>
                                    <td>{{ $sec->code }}</td>
                                    <td><a href="{{ route('voyager.sections.show',$sec->id) }}">{{ $sec->name }}</a>
                                    </td>
                                    <td>{{ ucfirst($sec->gender_scope) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sec->is_active?'success':'secondary' }}">{{ $sec->is_active?'Yes':'No' }}</span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('voyager.sections.edit',$sec->id) }}"
                                           class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No sections yet.</td>
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
