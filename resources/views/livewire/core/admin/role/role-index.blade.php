@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <!-- Zero Configuration  Starts-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header flex">
                    <h5>Roles</h5>
                    <a class="btn btn-primary" href="{{ route('roles.create') }}">Create Role</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->getAllPermissions()->pluck('name')->implode(',') }}</td>
                                        <td>
                                            <a class="btn btn-primary"
                                                href="{{ route('roles.edit', ['roleName' => $role->name]) }}">edit</a>
                                        </td>

                                    </tr>
                                @empty
                                    <p>No users found</p>
                                @endforelse



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session()->has('toastMagic'))
        <script>
            document.addEventListener('livewire:navigated', function() {
                Livewire.dispatch('toastMagic', @js(session('toastMagic')));
            });
        </script>
    @endif
</div>


@push('scripts')
    <script src="{{ asset('./assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('./assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('./assets/js/tooltip-init.js') }}"></script>
@endpush
