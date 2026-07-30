@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
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
                                <select class="js-example-basic-single form-control" id="role-select" {{ $isEdit ? 'disabled' : '' }}>
                                    <option value="">--select role--</option>
                                    @forelse ($this->roles as $roleItem)
                                        <option value="{{ $roleItem->name }}">{{ $roleItem->name }}</option>
                                    @empty
                                        <option disabled>No role found</option>
                                    @endforelse
                                </select>
                                @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            {{-- Permissions Multi-Selection --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label fw-medium">Assigned Permissions</label>
                                <select class="js-example-placeholder-multiple form-control" multiple="multiple"
                                    id="permissions-select">
                                    @forelse ($this->permissionNames as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @empty
                                        <option disabled>No permissions found</option>
                                    @endforelse
                                </select>
                                @error('permission') <small class="text-danger d-block mt-1">{{ $message }}</small>
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
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const elPermission = $("#permissions-select");
            const elRole = $("#role-select");

            function initSelect2() {
                elRole.select2({
                    placeholder: "-- Select Role --",
                    allowClear: !{{ $isEdit ? 'true' : 'false' }},
                    width: '100%'
                });

                elPermission.select2({
                    placeholder: "-- Select Permissions --",
                    allowClear: true,
                    closeOnSelect: false,
                    width: '100%'
                });
            }

            // Initialize Plugin Elements
            initSelect2();

            // Sync local changes upstream cleanly with Livewire core state engine
            elRole.on('change', function () {
                @this.set('role', $(this).val());
            });

            elPermission.on('change', function () {
                @this.set('permissions', $(this).val());
            });

            // Listen for customized component-emitted state reset hooks
            Livewire.on('resetSelectedPermissions', (event) => {
                const values = Array.isArray(event) ? event : (event.values || []);
                elPermission.val(values).trigger('change.select2');
            });

            Livewire.on('resetRoleName', (event) => {
                const values = Array.isArray(event) ? event : (event.values || '');
                elRole.val(values).trigger('change.select2');
            });

            // CRITICAL: Reinitialize Select2 after any Livewire morph DOM update cycles
            Livewire.hook('morph.updated', () => {
                initSelect2();
            });
        }); 
    </script>
@endpush