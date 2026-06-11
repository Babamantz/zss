@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush

<div class="container-fluid">
    <!-- Filter Bar -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">Filter by Role</label>
                    <select wire:model.live="filterRole" class="form-control form-control-sm">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">Filter by Tenant</label>
                    <select wire:model.live="filterTenant" class="form-control form-control-sm">
                        <option value="">All Tenants</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">Status</label>
                    <select wire:model.live="filterStatus" class="form-control form-control-sm">
                        <option value="active">Active</option>
                        <option value="in-active">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="downloadReport" class="btn btn-primary w-100 btn-sm">
                        <i class="icofont icofont-download"></i> Export User Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 display" id="basic-1">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role(s)</th>
                        <th>Tenant</th>
                        <th>Status</th>
                        <th>Date Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-light text-primary border">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->tenant->name ?? 'System Admin' }}</td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>

                            </td>
                            <td>{{ $user->created_at->format('d M, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@push('scripts')
    <script src="{{ asset('./assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('./assets/js/datatable/datatables/datatable.custom.js') }}"></script>
@endpush
