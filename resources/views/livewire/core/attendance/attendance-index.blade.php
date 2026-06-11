{{-- resources/views/livewire/attendance/attendance-index.blade.php --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>My Attendance Forms</h5>
                            <a href="{{ route('attendance.create') }}" class="btn btn-primary" wire:navigate>
                                <i class="icon-plus"></i> Create New Form
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Filters --}}
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" placeholder="Search forms..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" wire:model.live="langFilter">
                                    <option value="all">All Languages</option>
                                    <option value="en">English</option>
                                    <option value="sw">Swahili</option>
                                </select>
                            </div>
                        </div>

                        {{-- Attendance Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Languages</th>
                                        <th>Rows</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendances as $attendance)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if (in_array('en', $attendance->languages))
                                                    <strong>EN:</strong> {{ $attendance->title['en'] ?? 'N/A' }}<br>
                                                @endif
                                                @if (in_array('sw', $attendance->languages))
                                                    <strong>SW:</strong> {{ $attendance->title['sw'] ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($attendance->languages as $lang)
                                                    <span
                                                        class="badge badge-{{ $lang === 'en' ? 'primary' : 'success' }}">
                                                        {{ strtoupper($lang) }}
                                                    </span>
                                                @endforeach
                                                <br>
                                                <small class="text-muted">Default:
                                                    {{ strtoupper($attendance->default_lang) }}</small>
                                            </td>
                                            <td>{{ $attendance->number_of_rows }}</td>
                                            <td>{{ $attendance->created_at->format('M d, Y') }}</td>
                                            <td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a class="btn btn-info btn-sm"
                                                        href="{{ route('attendance.edit', ['attendanceId' => $attendance->id, 'mode' => 'view']) }}"
                                                        wire:navigate>View</a>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('attendance.edit', ['attendanceId' => $attendance->id]) }}"
                                                        wire:navigate>Edit</a>
                                                    <a class="btn btn-success btn-sm"
                                                        href="{{ route('attendance.preview', $attendance->id) }}"
                                                        target="_blank">Preview</a>
                                                    <button class="btn btn-danger btn-sm"
                                                        wire:click="confirmDelete({{ $attendance->id }})">Delete</button>
                                                </div>
                                            </td>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No attendance forms found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $attendances->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($confirmingDeletion)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close"
                            wire:click="$set('confirmingDeletion', false)"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this attendance form? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('confirmingDeletion', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteAttendance">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
