@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush
<div class="container-fluid">
    <!-- Filter Bar -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body bg-light">
            <div class="row">
                <div class="col-md-3">
                    <label class="fw-bold">Gender</label>
                    <select wire:model.live="filterGender" class="form-control">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold">Department</label>
                    <select wire:model.live="filterDept" class="form-control">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold">Status</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="active">Active</option>
                        <option value="in-active">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="downloadReport" class="btn btn-primary w-100">
                        <i class="icofont icofont-download"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover mb-0 display" id="basic-1">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Unit</th>
                    <th>Hired Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $emp)
                    <tr>
                        <td>{{ $emp->user->first_name }} {{ $emp->user->last_name }} {{ $emp->user->last_name }}</td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($emp->gender) }}</span></td>
                        <td>{{ $emp->designation }}</td>
                        <td>{{ $emp->department?->name ?? 'N/A' }}</td>
                        <td>{{ $emp->unit?->name ?? 'N/A' }}</td>
                        <td>{{ $emp->hired_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</div>

@push('scripts')
    <script src="{{ asset('./assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('./assets/js/datatable/datatables/datatable.custom.js') }}"></script>
@endpush
