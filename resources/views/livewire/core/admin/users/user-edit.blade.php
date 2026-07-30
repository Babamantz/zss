@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select.css') }}">
    <!-- Optional: include when using Bootstrap 4 theme styling -->
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select-bootstrap-v4.css') }}">

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
                            </div>
                            @error('first_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror

                            {{-- Middle Name --}}
                            <div class="col-md-4">
                                <label class="form-label" for="Mname">Middle Name</label>
                                <input class="form-control" id="Mname" type="text" wire:model="middle_name"
                                    placeholder="Robert">

                            </div>
                            @error('middle_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror

                            {{-- Last Name --}}
                            <div class="col-md-4">
                                <label class="form-label" for="lname">Last Name</label>
                                <input class="form-control" id="lname" type="text" wire:model="last_name"
                                    placeholder="Doe">

                            </div>
                            @error('last_name') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror

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

                            </div>
                            @error('is_officer') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror

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
                            </div>
                            @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror


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

                            </div>
                            @error('location') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror

                            {{-- Role Dropdown Select --}}
                            <div class="col-md-6" wire:ignore>
                                <label class="form-label">Role</label>
                                <livewire:async-select name="roles" wire:model="role" :options="$this->roleNames"
                                    placeholder="Select role..." />
                            </div>
                            @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror


                            {{-- Permissions Dropdown Select --}}
                            <div class="col-12" wire:ignore>
                                <label class="form-label">Direct Permissions</label>
                                <livewire:async-select name="direct_permissions[]" wire:model="direct_permissions"
                                    :options="$this->permissionNames" :multiple="true"
                                    placeholder="Select permission..." />
                            </div>
                            @error('direct_permissions') <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
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

@endpush