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
                    <h5>Employees</h5>
                    <a class="btn btn-primary" href="{{ route('hrm.employees.create') }}">Add Employee</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="basic-1">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Unit</th>
                                    <th>Department</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr wire:key="user-{{ $employee->user?->id }}">
                                        <!-- Combined Name -->
                                        <td>{{ "{$employee->user->first_name} {$employee->user?->middle_name} {$employee->user?->last_name}" }}
                                        </td>

                                        <td>{{ $employee->user?->email }}</td>

                                        <!-- Null-safe Tenant Name -->

                                        <!-- Role Name (Assumes Spatie or similar method) -->
                                        <td>{{ $employee->phone_number }}</td>
                                        <td>{{ $employee->user?->tenant?->name ?? 'N/A' }}</td>

                                        <td>
                                            {{ $employee->unit?->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $employee->department?->name ?? 'N/A' }}
                                        </td>

                                        <td>
                                            <a class="btn btn-primary"
                                                href="{{ route('hrm.employee.edit', ['employeeId' => $employee->id, 'mode' => 'view']) }}"
                                                wire:navigate>view</a>
                                            <a class="btn btn-primary"
                                                href="{{ route('hrm.employee.edit', ['employeeId' => $employee->id]) }}"
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
