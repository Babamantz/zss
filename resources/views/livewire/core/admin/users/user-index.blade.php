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

</div>


@push('scripts')
    <script src="{{ asset('./assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('./assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
@endpush
