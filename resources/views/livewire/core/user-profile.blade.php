<div class="container-fluid">

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Sidebar: Profile Summary -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary py-4 text-center">
                    <img class="img-90 rounded-circle border border-white mb-3"
                        src="{{ $employee && $employee->photo_file ? asset('storage/' . $employee->photo_file) : asset('assets/images/dashboard/1.png') }}"
                        alt="">
                    <h5 class="mb-0 text-white">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-white-50 small">{{ $employee->designation?->designation_name ?? 'Role Not Set' }}</p>
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
                            <span>{{ $employee->division?->department->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Tenant</span>
                            <span class="text-primary fw-bold">{{ $user->tenant->name ?? 'System' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-light border-0">
                    <p class="small mb-1 text-muted">Profile Completion</p>
                    <div class="progress sm-progress-bar mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completion }}%">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100"
                        wire:click="openChangePasswordModal">
                        <i class="fa fa-lock me-1"></i> Change Password
                    </button>
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
                            <p class="fw-bold">{{ $employee->opf_number ?? 'Not Assigned' }}</p>
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
                </div>
            </div>
        </div>
    </div>

    {{-- ── Change Password Modal ───────────────────────────────────────────── --}}
    @if ($showChangePasswordModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form wire:submit.prevent="updatePassword">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Password</h5>
                            <button type="button" class="btn-close" wire:click="closeChangePasswordModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <div class="input-group" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" class="form-control"
                                        wire:model.defer="current_password" autocomplete="current-password">
                                    <button type="button" class="btn btn-outline-secondary" @click="show = !show"
                                        tabindex="-1">
                                        <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" class="form-control"
                                        wire:model.defer="new_password" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" @click="show = !show"
                                        tabindex="-1">
                                        <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('new_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" class="form-control"
                                        wire:model.defer="new_password_confirmation" autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" @click="show = !show"
                                        tabindex="-1">
                                        <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeChangePasswordModal">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="updatePassword">
                                <span wire:loading.remove wire:target="updatePassword">
                                    <i class="fa fa-lock me-1"></i> Update Password
                                </span>
                                <span wire:loading wire:target="updatePassword">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>