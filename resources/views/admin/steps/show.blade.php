@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Step: {{ $step->title }}</h4>
            <a href="{{ route('voyager.steps.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2"><strong>Studio:</strong> {{ $step->studio?->name }}</div>
                        <div class="mb-2"><strong>Code:</strong> {{ $step->code }}</div>
                        <div class="mb-2"><strong>Order:</strong> {{ $step->order }}</div>
                        <div class="mb-2"><strong>Active:</strong> <span
                                    class="badge bg-{{ $step->is_active?'success':'secondary' }}">{{ $step->is_active?'Yes':'No' }}</span>
                        </div>
                        <div class="mb-2"><strong>Subtitle:</strong> {{ $step->subtitle ?: '—' }}</div>
                        <div class="mb-2"><strong>Icon:</strong> {{ $step->icon ?: '—' }}</div>
                        <div><strong>Tip:</strong><br>{{ $step->tip_text ?: '—' }}</div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header"><strong>Add Sections to this Step</strong></div>
                    <div class="card-body">
                        @if($eligible->isEmpty())
                            <div class="text-muted">No eligible sections (either all are assigned to other steps or
                                inactive).
                            </div>
                        @else
                            <form action="{{ route('voyager.steps.add-sections',$step->id) }}" method="post">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">Select Sections</label>
                                    <select name="section_ids[]" class="form-select" multiple size="8">
                                        @foreach($eligible as $sec)
                                            <option value="{{ $sec->id }}">
                                                [{{ strtoupper($sec->gender_scope) }}] {{ $sec->name }}
                                                @if($sec->group_id)
                                                    — (Group: {{ $sec->group?->name ?? $sec->group_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                                </div>
                                <button class="btn btn-primary w-100">Add Selected</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Sections in this Step</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Group</th>
                                <th>Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($step->stepSections as $p)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $p->order }}
                                        <a href="{{ route('voyager.steps.move-up',[$step->id,$p->id]) }}"
                                           class="btn btn-sm btn-light">↑</a>
                                        <a href="{{ route('voyager.steps.move-down',[$step->id,$p->id]) }}"
                                           class="btn btn-sm btn-light">↓</a>
                                    </td>
                                    <td>{{ $p->section?->code }}</td>
                                    <td>
                                        <a href="{{ route('voyager.sections.show', $p->section?->id) }}">{{ $p->section?->name }}</a>
                                    </td>
                                    <td>{{ ucfirst($p->section?->gender_scope) }}</td>
                                    <td>{{ $p->section?->group?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $p->is_active ? 'success' : 'secondary' }}">{{ $p->is_active ? 'Yes' : 'No' }}</span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('voyager.steps.toggle-pivot',[$step->id,$p->id]) }}"
                                           class="btn btn-sm btn-outline-secondary">Toggle</a>
                                        <form action="{{ route('voyager.steps.remove-section',[$step->id,$p->id]) }}"
                                              method="post" class="d-inline"
                                              onsubmit="return confirm('Remove this section from the step?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No sections assigned yet.</td>
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
