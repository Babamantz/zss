@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Admin User Create</h5>
                    <a class="btn btn-primary" href="{{ route('users.index') }}">Back</a>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent='save'>
                        <div class="tab">
                            <div class="form-group">
                                <label for="fname">First Name</label>
                                <input class="form-control" id="name" type="text" wire:model="first_name"
                                    required="required">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="Mname">Middle Name</label>
                                <input class="form-control" id="Mname" type="text" wire:model="middle_name">
                                @error('middle_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="lname">Last Name</label>
                                <input class="form-control digits" id="lname" type="text" wire:model="last_name">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input class="form-control digits" id="email" type="email" wire:model="email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="mb-2">
                                    <label class="col-form-label">Location</label>
                                    <select class="form-control form-control-info btn-square" name="select"
                                        wire:model="location">
                                        <option value="">--select location--</option>
                                        @forelse ($this->locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @empty
                                            <option disabled>No location found</option>
                                        @endforelse
                                    </select>
                                    @error('location')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group" wire:ignore>
                                <label class="col-form-label">Role</label>
                                <select class="js-example-basic-single col-sm-12" id="role-select"
                                    wire:model.defer="role">
                                    <option value="">--select role--</option>
                                    @forelse ($this->roleNames as $roleItem)
                                        <option value="{{ $roleItem->name }}">{{ $roleItem->name }}</option>
                                    @empty
                                        <option disabled>No roles found</option>
                                    @endforelse
                                </select>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group" wire:ignore>
                                <label class="col-form-label">Direct Permissions</label>
                                <select class="js-example-placeholder-multiple col-sm-12" multiple="multiple"
                                    id="permissions-select" wire:model="direct_permissions">
                                    @forelse ($this->permissionNames as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @empty
                                        <option disabled>No permissions found</option>
                                    @endforelse
                                </select>
                                @error('direct_permissions')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <div class="text-end btn-mb">
                                <button class="btn btn-primary" type="submit">Submit</button>
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
            // Sync Select2 role changes with Livewire
            $('#role-select').on('change', function(e) {
                @this.set('role', e.target.value);
            });

            // Sync Select2 permissions changes with Livewire
            $('#permissions-select').on('change', function(e) {
                let values = $(this).val();
                @this.set('direct_permissions', values || []);
            });
        });
    </script>
@endpush
