{{-- resources/views/livewire/documents/document-form.blade.php --}}
<div>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>{{ $isEditMode ? ($isViewMode ? 'View Document' : 'Edit Document') : 'Create Document' }}</h4>
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

                <form wire:submit.prevent="submitForm">
                    {{-- Document Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Document Name <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            wire:model="name" {{ $isViewMode ? 'disabled' : '' }}>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- File Upload --}}
                    @if (!$isViewMode)
                        <div class="mb-3">
                            <label for="document" class="form-label">
                                Upload Document <span class="text-danger">{{ $isEditMode ? '' : '*' }}</span>
                            </label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror"
                                id="document" wire:model="document">
                            <small class="text-muted">Allowed: PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Loading indicator --}}
                            <div wire:loading wire:target="document" class="text-primary mt-2">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Uploading...
                            </div>
                        </div>
                    @endif

                    {{-- Existing Document --}}
                    @if ($existingDocumentPath)
                        <div class="mb-3">
                            <label class="form-label">Current Document</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info">{{ basename($existingDocumentPath) }}</span>
                                <button type="button" class="btn btn-sm btn-success" wire:click="downloadDocument">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-4">
                        <a href="{{ route('documents.index') }}" class="btn btn-secondary" wire:navigate>Back</a>

                        @if (!$isViewMode)
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitForm">
                                    {{ $isEditMode ? 'Update' : 'Submit' }}
                                </span>
                                <span wire:loading wire:target="submitForm">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Saving...
                                </span>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
