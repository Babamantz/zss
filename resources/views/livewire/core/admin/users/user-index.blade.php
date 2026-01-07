@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <!-- Zero Configuration  Starts-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Users</h5>
                    <a class="btn btn-primary" href="{{ route('users.create') }}">Create User</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr wire:key="user-{{ $user->id }}">
                                        <!-- Combined Name -->
                                        <td>{{ "{$user->first_name} {$user->middle_name} {$user->last_name}" }}</td>

                                        <td>{{ $user->email }}</td>

                                        <!-- Null-safe Tenant Name -->
                                        <td>{{ $user->tenant->name ?? 'N/A' }}</td>

                                        <!-- Role Name (Assumes Spatie or similar method) -->
                                        <td>{{ $user->getRoleNames()->implode(', ') }}</td>

                                        <!-- Permissions List -->
                                        <td>
                                            <small class="text-muted">
                                                {{ $user->getAllPermissions()->pluck('name')->implode(', ') }}
                                            </small>
                                        </td>

                                        <td>
                                            <a class="btn btn-primary"
                                                href="{{ route('users.edit', ['id' => $user->id, 'mode' => 'view']) }}"
                                                wire:navigate>view</a>
                                            <a class="btn btn-primary"
                                                href="{{ route('users.edit', ['id' => $user->id, 'mode' => 'edit']) }}"
                                                wire:navigate>edit</a>
                                            {{-- <a class="btn btn-primary" wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="Are you sure you want to delete this user?">delete</a> --}}
                                        </td>
                                    </tr>

                                @empty
                                    <p>No user found</p>
                                @endforelse



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- <div x-show="" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-4">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View User</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">

                                <form wire:submit.prevent="save">

                                    <!-- Row 1 -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>First Name</label>
                                            <input class="form-control" type="text" wire:model="first_name">
                                            @error('first_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Middle Name</label>
                                            <input class="form-control" type="text" wire:model="middle_name">
                                            @error('middle_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Row 2 -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Last Name</label>
                                            <input class="form-control" type="text" wire:model="last_name">
                                            @error('last_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Email</label>
                                            <input class="form-control" type="email" wire:model="email">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Row 3 -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Location</label>
                                            <select class="form-control" wire:model="location">
                                                <option value="">--select location--</option>
                                                @foreach ($this->locations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('location')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3" wire:ignore>
                                            <label>Role</label>
                                            <select class="js-example-basic-single form-control"
                                                wire:model.defer="role">
                                                <option value="">--select role--</option>
                                                @foreach ($this->roleNames as $roleItem)
                                                    <option value="{{ $roleItem->name }}">
                                                        {{ $roleItem->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Permissions (Full Width / Colspan 2) -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3" wire:ignore>
                                            <label>Direct Permissions</label>
                                            <select class="js-example-placeholder-multiple form-control" multiple
                                                wire:model="direct_permissions">
                                                @foreach ($this->permissionNames as $permission)
                                                    <option value="{{ $permission->name }}">
                                                        {{ $permission->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('direct_permissions')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </div>
        </div>
    </div> --}}

</div>


@push('scripts')
    <script src="{{ asset('./assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('./assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
@endpush
