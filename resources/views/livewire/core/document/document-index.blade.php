{{-- resources/views/livewire/documents/document-index.blade.php --}}
<div>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Documents</h4>
                <a href="{{ route('documents.create') }}" class="btn btn-primary" wire:navigate>
                    <i class="bi bi-plus-circle"></i> Add Document
                </a>
            </div>

            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Search --}}
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Search documents..."
                        wire:model.live.debounce.300ms="search">
                </div>

                {{-- Documents Table --}}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Uploaded Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $document)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info"
                                            href="{{ route('documents.edit', ['documentId' => $document->id, 'mode' => 'view']) }}"
                                            wire:navigate>View</a>
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('documents.edit', ['documentId' => $document->id]) }}"
                                            wire:navigate>Edit</a>
                                        <a class="btn btn-sm btn-success"
                                            href="{{ Storage::url($document->document_path) }}"
                                            target="_blank">Download</a>
                                        <button class="btn btn-sm btn-danger"
                                            wire:click="confirmDelete({{ $document->id }})">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No documents found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $documents->links() }}
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
                        Are you sure you want to delete this document? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('confirmingDeletion', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteDocument">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
