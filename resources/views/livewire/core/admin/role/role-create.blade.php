@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select.css') }}">
    <!-- Optional: include when using Bootstrap 4 theme styling -->
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select-bootstrap-v4.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h5 class="fw-bold text-dark">{{ $isEdit ? 'Edit Role Permissions' : 'Create Role Permissions' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div
                            class="section-title mb-4 pb-2 border-bottom text-muted small text-uppercase fw-semibold tracking-wider">
                            Role & Permissions Assignment
                        </div>

                        <div class="row g-4">
                            {{-- Role Selection --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label fw-medium">Role Name</label>
                                <livewire:async-select name="roles" wire:model="role" :options="$this->roles"
                                    placeholder="Select country..." />
                                @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            {{-- Permissions Multi-Selection --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label fw-medium">Assigned Permissions</label>
                                <livewire:async-select name="permissions[]" wire:model="permissions" :options="$this->permissionNames" :multiple="true"
                                    placeholder="Select permisssions..." />

                                @error('permissions') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Bar: Balanced Layout Mechanics --}}
                        <div class="action-bar d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left me-1"></i> Back to Roles
                            </a>
                            <div>
                                <button class="btn btn-outline-secondary me-2" type="reset"> Cancel </button>
                                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> {{ $isEdit ? 'Update Permissions' : 'Save Setup' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

@endpush