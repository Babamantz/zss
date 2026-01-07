@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/.assets/css/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $isEdit ? 'Edit Role Permission ' : 'Create Role Permissions' }}</h5>
                    <a class="btn btn-primary" href="{{ route('roles.index') }}">Back</a>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="tab">
                            <div class="mb-2" wire:ignore>
                                <label class="col-form-label">Role</label>
                                <select class="js-example-basic-single col-sm-12" id="role-select"
                                    wire:model.defer="role" {{ $isEdit ? 'disabled' : '' }}>

                                    <option value="">--select role--</option>
                                    @forelse ($this->roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @empty
                                        <option disabled>No role found</option>
                                    @endforelse
                                </select>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-2" wire:ignore>
                                <label class="col-form-label">Permission</label>
                                <select class="js-example-placeholder-multiple col-sm-12" multiple="multiple"
                                    id="permissions-select" wire:model.defer="permissions">
                                    <option value="">--select permission--</option>
                                    @forelse ($this->permissionNames as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @empty
                                        <option disabled>No permissions found</option>
                                    @endforelse
                                </select>
                                @error('permission')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <div class="text-end btn-mb">
                                    <button class="btn btn-primary"
                                        type="submit">{{ $isEdit ? 'Update' : 'Create' }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
@push('scripts')
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
    {{-- <script>
        document.addEventListener('livewire:initialized', () => {
            // Sync Select2 role changes with Livewire

            let elPermission = $("#permissions-select")
            let elRole = $("#role-select")
            Livewire.on('resetSelectedPermissions', (values) => {
                elPermission.val(values).trigger('change.select2'); // Reset Select2 for permissions
            });

            $('#permissions-select').on('change', function() {
                @this.set('permissions', $(this).val());
            });

            Livewire.on('resetRoleName', (values) => {
                elRole.val(values).trigger('change.select2'); // Reset Select2 for role name
            });

        });
    </script> --}}

    <script>
        document.addEventListener('livewire:initialized', () => {
            const elPermission = $("#permissions-select");
            const elRole = $("#role-select");

            // Initialize Select2 with better options
            elRole.select2({
                placeholder: "-- Select Role --",
                allowClear: !{{ $isEdit ? 'true' : 'false' }},
                theme: 'bootstrap'
            });

            elPermission.select2({
                placeholder: "-- Select Permissions --",
                allowClear: true,
                theme: 'bootstrap',
                closeOnSelect: false
            });

            // Sync role changes with Livewire
            elRole.on('change', function() {
                @this.set('role', $(this).val());
            });

            // Sync permissions changes with Livewire
            elPermission.on('change', function() {
                @this.set('permissions', $(this).val());
            });

            // Listen for Livewire events to reset Select2 values
            Livewire.on('resetSelectedPermissions', (values) => {
                elPermission.val(values).trigger('change.select2');
            });

            Livewire.on('resetRoleName', (values) => {
                elRole.val(values).trigger('change.select2');
            });

            // Reinitialize Select2 after Livewire updates
            // Livewire.hook('morph.updated', () => {
            //     elRole.select2({
            //         placeholder: "-- Select Role --",
            //         allowClear: !{{ $isEdit ? 'true' : 'false' }},
            //         theme: 'bootstrap'
            //     });

            //     elPermission.select2({
            //         placeholder: "-- Select Permissions --",
            //         allowClear: true,
            //         theme: 'bootstrap',
            //         closeOnSelect: false
            //     });
            // });
        });
    </script>
@endpush
