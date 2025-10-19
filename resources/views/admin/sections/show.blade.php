@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Section: {{ $section->name }}</h4>
            <a href="{{ route('voyager.sections.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2"><strong>Studio:</strong> {{ $section->studio?->name }}</div>
                        <div class="mb-2"><strong>Group:</strong> {{ $section->group?->name ?? '—' }}</div>
                        <div class="mb-2"><strong>Code:</strong> {{ $section->code }}</div>
                        <div class="mb-2"><strong>Type:</strong> {{ ucfirst($section->input_type) }}</div>
                        <div class="mb-2"><strong>Mode:</strong> {{ ucfirst($section->selection_mode) }}</div>
                        <div class="mb-2"><strong>Gender:</strong> {{ ucfirst($section->gender_scope) }}</div>
                        <div class="mb-2"><strong>Order:</strong> {{ $section->order }}</div>
                        <div class="mb-2"><strong>General:</strong> {{ $section->is_general ? 'Yes' : 'No' }}</div>
                        <div class="mb-2"><strong>Required:</strong> {{ $section->is_required ? 'Yes' : 'No' }}</div>
                        <div class="mb-2"><strong>Active:</strong>
                            <span class="badge bg-{{ $section->is_active ? 'success' : 'secondary' }}">{{ $section->is_active ? 'Yes' : 'No' }}</span>
                        </div>
                        @if($section->help_text)
                            <div><strong>Help Text:</strong><br>{{ $section->help_text }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Choices</strong>
                        <a href="{{ route('voyager.choices.create') }}?section_id={{ $section->id }}"
                           class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Add Choice
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Label</th>
                                <th>Slug</th>
                                <th>Gender</th>
                                <th>Default</th>
                                <th>Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($section->choices as $c)
                                <tr>
                                    <td>{{ $c->order }}</td>
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
                                    <td colspan="7" class="text-center text-muted py-4">No choices yet.</td>
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
