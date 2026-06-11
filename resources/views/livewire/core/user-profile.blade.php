<div class="container-fluid">
    <div class="row">
        <!-- Left Sidebar: Profile Summary -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary py-4 text-center">
                    <img class="img-90 rounded-circle border border-white mb-3"
                        src="{{ $employee && $employee->photo_file ? asset('storage/' . $employee->photo_file) : asset('assets/images/dashboard/1.png') }}"
                        alt="">
                    <h5 class="mb-0 text-white">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-white-50 small">{{ $employee->designation ?? 'Role Not Set' }}</p>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Department</span>
                            <span>{{ $employee->department->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Tenant</span>
                            <span class="text-primary fw-bold">{{ $user->tenant->name ?? 'System' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light border-0">
                    <p class="small mb-1 text-muted">Profile Completion</p>
                    <div class="progress sm-progress-bar">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completion }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Detailed Tabs -->
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-4 border-bottom pb-2">Professional Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Employee ID (OPF)</label>
                            <p class="fw-bold">{{ $employee->opf_number?? 'Not Assigned' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Social Security No</label>
                            <p class="fw-bold">{{ $employee->social_security_no ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Education Level</label>
                            <p class="fw-bold">{{ ucfirst($employee->education ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Hired Date</label>
                            <p class="fw-bold text-info">
                                {{ $employee->hired_date ? \Carbon\Carbon::parse($employee->hired_date)->format('d M, Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-5 mb-4 border-bottom pb-2">Compliance Documents</h6>
                    <div class="row text-center">
                        @php $docs = ['NIDA' => 'nida_file', 'ZAN ID' => 'zan_id_file', 'Contract' => 'employment_contract_file']; @endphp
                        @foreach ($docs as $label => $field)
                            <div class="col-md-4">
                                <div
                                    class="p-3 border rounded {{ $employee && $employee->$field ? 'border-success bg-light-success' : 'border-dashed' }}">
                                    <i
                                        class="icofont {{ $employee && $employee->$field ? 'icofont-check-circled text-success' : 'icofont-warning text-warning' }} fa-2x mb-2"></i>
                                    <p class="small mb-0 fw-bold">{{ $label }}</p>
                                    @if ($employee && $employee->$field)
                                        <a href="#" class="btn btn-xs btn-primary mt-2">View</a>
                                    @else
                                        <span class="text-danger small">Missing</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
