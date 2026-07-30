@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $this->isEdit ? 'Admin User Edit' : 'Admin User Create' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent='save'>
                        <div class="section-title mb-4"> User Information </div>

                        <div class="row g-4">
                            {{-- First Name --}}
                            <div class="col-md-4">
                                <label class="form-label" for="name">First Name</label>
                                <input class="form-control" id="name" type="text" wire:model="first_name"
                                    required="required" placeholder="John">
                                @error('first_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div class="col-md-4">
                                <label class="form-label" for="Mname">Middle Name</label>
                                <input class="form-control" id="Mname" type="text" wire:model="middle_name"
                                    placeholder="Robert">
                                @error('middle_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-4">
                                <label class="form-label" for="lname">Last Name</label>
                                <input class="form-control" id="lname" type="text" wire:model="last_name"
                                    placeholder="Doe">
                                @error('last_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Is Officer? --}}
                            <div class="col-md-6">
                                <label class="form-label"> Is Officer? </label>
                                <div class="radio-card d-flex gap-3 p-2 border rounded bg-light">
                                    <label class="radio-option mb-0 cursor-pointer">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="radio" wire:model="is_officer"
                                                value="1" id="officer_yes">
                                            <label class="form-check-label ms-1" for="officer_yes"> Yes </label>
                                        </div>
                                    </label>
                                    <label class="radio-option mb-0 cursor-pointer">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="radio" wire:model="is_officer"
                                                value="0" id="officer_no">
                                            <label class="form-check-label ms-1" for="officer_no"> No </label>
                                        </div>
                                    </label>
                                </div>
                                @error('is_officer') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Is Active? --}}
                            <div class="col-md-6">
                                <label class="form-label">Status (Is Active?)</label>

                                <div class="radio-card d-flex gap-3 p-2 border rounded bg-light">

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            wire:model.live="is_active" value="1" id="active_yes">
                                        <label class="form-check-label" for="active_yes">
                                            Active
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_active"
                                            wire:model.live="is_active" value="0" id="active_no">
                                        <label class="form-check-label" for="active_no">
                                            Inactive
                                        </label>
                                    </div>

                                </div>
                            </div>


                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email Address</label>
                                <input class="form-control" id="email" type="email" wire:model="email"
                                    placeholder="john@example.com">
                                @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            {{-- Location --}}
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <select class="form-select" wire:model="location">
                                    <option value="">--select location--</option>
                                    @forelse ($this->locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @empty
                                        <option disabled>No location found</option>
                                    @endforelse
                                </select>
                                @error('location') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Role Dropdown Select --}}
                            <div class="col-md-6" wire:ignore>
                                <label class="form-label">Role</label>
                                <select class="js-example-basic-single form-control" id="role-select">
                                    <option value="">--select role--</option>
                                    @forelse ($this->roleNames as $roleItem)
                                        <option value="{{ $roleItem->name }}" {{ $role == $roleItem->name ? 'selected' : '' }}>{{ $roleItem->name }}</option>
                                    @empty
                                        <option disabled>No roles found</option>
                                    @endforelse
                                </select>
                                @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            {{-- Permissions Dropdown Select --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label">Direct Permissions</label>
                                <select class="js-example-placeholder-multiple form-control" multiple="multiple"
                                    id="permissions-select">
                                    @forelse ($this->permissionNames as $permission)
                                        <option value="{{ $permission->name }}" {{ in_array($permission->name, $direct_permissions) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                    @empty
                                        <option disabled>No permissions found</option>
                                    @endforelse
                                </select>
                                @error('direct_permissions') <small
                                class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Action Bar --}}
                        <div class="action-bar d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left me-1"></i> Back to Users
                            </a>
                            <div>
                                <button class="btn btn-outline-secondary me-2" type="reset"> Cancel </button>
                                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> {{ $this->isEdit ? 'Update User' : 'Save User' }}
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
        function initUserEditSelect2() {
            const $role = $('#role-select');
            const $perms = $('#permissions-select');

            if ($role.length && !$role.hasClass('select2-hidden-accessible')) {
                $role.select2({ width: '100%' });
            }
            if ($perms.length && !$perms.hasClass('select2-hidden-accessible')) {
                $perms.select2({ width: '100%', placeholder: '--select permissions--' });
            }
        }

        // Delegated on document — survives full reloads AND wire:navigate swaps,
        // since the handler isn't tied to a specific (possibly-replaced) DOM node.
        $(document).off('change', '#role-select').on('change', '#role-select', function (e) {
            @this.set('role', e.target.value);
        });

        $(document).off('change', '#permissions-select').on('change', '#permissions-select', function () {
            @this.set('direct_permissions', $(this).val() || []);
        });

        // Fires on the initial page load AND after every wire:navigate transition —
        // unlike 'livewire:initialized', which only fires once ever.
        document.addEventListener('livewire:navigated', initUserEditSelect2);

        // Cover the very first paint (before any navigation event exists yet)
        initUserEditSelect2();

        // Keep the widget's displayed selection in sync with server-side state
        // (e.g. after validation errors reset other fields but role should persist)
        Livewire.on('refreshSelect2', (data) => {
            $('#role-select').val(data.role).trigger('change.select2');
            $('#permissions-select').val(data.permissions).trigger('change.select2');
        });
    </script>
@endpush