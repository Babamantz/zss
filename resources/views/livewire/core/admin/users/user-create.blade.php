@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select.css') }}">
    <!-- Optional: include when using Bootstrap 4 theme styling -->
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select-bootstrap-v4.css') }}">

    <style>
        .form-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 .125rem .35rem rgba(0, 0, 0, .06);
        }

        .section-title {
            font-size: .85rem;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: .45rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            min-height: 44px;
        }

        .select2-container--default .select2-selection--single {
            height: 44px;
            border-radius: 10px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 44px;
            border-radius: 10px;
            border: 1px solid #ced4da;
        }

        .radio-card {
            display: flex;
            gap: 15px;
        }

        .radio-card label {
            margin-bottom: 0;
        }

        .radio-option {
            flex: 1;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            transition: .2s;
        }

        .radio-option:hover {
            border-color: #0d6efd;
            background: #f8fbff;
        }

        .action-bar {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
@endpush
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Create User</h4>
            <small class="text-muted">
                Register a new system user and assign permissions.
            </small>
        </div>
    </div>

    <div class="card form-card">

        <div class="card-body">

            <form wire:submit.prevent="save">
                <div class="section-title"> User Information </div>

                <div class="row g-4">
                    {{-- First Name --}}
                    <div class="col-md-4">
                        <label class="form-label"> First Name </label>
                        <input type="text" class="form-control" wire:model.defer="first_name" placeholder="John">
                        @error('first_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Middle Name --}}
                    <div class="col-md-4">
                        <label class="form-label"> Middle Name </label>
                        <input type="text" class="form-control" wire:model.defer="middle_name" placeholder="Robert">
                        @error('middle_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="col-md-4">
                        <label class="form-label"> Last Name </label>
                        <input type="text" class="form-control" wire:model.defer="last_name" placeholder="Doe">
                        @error('last_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Officer --}}
                    <div class="col-md-6">
                        <label class="form-label"> Is Officer? </label>
                        <div class="radio-card">
                            <label class="radio-option">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.defer="is_officer"
                                        value="1">
                                    <span class="ms-2"> Yes </span>
                                </div>
                            </label>
                            <label class="radio-option">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.defer="is_officer"
                                        value="0">
                                    <span class="ms-2"> No </span>
                                </div>
                            </label>
                        </div>
                        @error('is_officer') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label"> Email Address </label>
                        <input type="email" class="form-control" wire:model.defer="email"
                            placeholder="john@example.com">
                        @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Location --}}
                    <div class="col-md-6">
                        <label class="form-label"> Location </label>
                        <select class="form-select" wire:model.defer="location">
                            <option value="">Select location</option>
                            @foreach($this->locations as $location)
                                <option value="{{ $location->id }}"> {{ $location->name }} </option>
                            @endforeach
                        </select>
                        @error('location') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label class="form-label"> Role </label>
                        <livewire:async-select name="roles" wire:model="role" :options="$this->roleNames"
                            placeholder="Select role..." />
                        @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    {{-- Permissions --}}
                    <div class="col-12">
                        <label class="form-label"> Direct Permissions </label>
                        <livewire:async-select name="direct_permissions[]" wire:model="direct_permissions"
                            :options="$this->permissionNames" :multiple="true" placeholder="Select permission..." />
                        @error('direct_permissions') <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- Action Bar --}}
                <div class="action-bar d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-dark">
                        <i class="fa fa-arrow-left me-1"></i> Back to Users
                    </a>
                    <div>
                        <button class="btn btn-outline-secondary me-2" type="reset"> Cancel </button>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fa fa-save me-1"></i> Save User
                        </button>
                    </div>
                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')

@endpush