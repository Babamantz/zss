{{-- resources/views/chain/livewire/chain-transaction-create.blade.php --}}

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">New Transaction</h4>
            <small class="text-muted">Submit a transaction for approval</small>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">
                        <i class="fa fa-paper-plane me-2 text-primary"></i>
                        Transaction Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            Chain Module <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" wire:model.live="moduleCode">
                            <option value="">-- Select Workflow --</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->code }}">
                                    {{ $module->name }}
                                    <span class="text-muted">({{ $module->code }})</span>
                                </option>
                            @endforeach
                        </select>
                        @error('moduleCode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        {{-- Show flow levels for selected module --}}
                        @if ($moduleCode)
                            @php
                                $selectedModule = $modules->firstWhere('code', $moduleCode);
                            @endphp
                            @if ($selectedModule)
                                <div class="mt-2 p-3 bg-light rounded-3">
                                    <small class="text-muted fw-medium d-block mb-2">
                                        Approval levels for this workflow:
                                    </small>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @foreach ($selectedModule->activeFlows as $flow)
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $flow->level_no }}. {{ $flow->level_name }}
                                                @if ($flow->role)
                                                    ({{ $flow->role->name }})
                                                @endif
                                            </span>
                                            @if (!$loop->last)
                                                <i class="fa fa-arrow-right text-muted align-self-center" style="font-size:10px;"></i>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model.defer="title"
                            placeholder="Brief description of the transaction">
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="4" wire:model.defer="description"
                            placeholder="Additional details (optional)"></textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" wire:click="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">
                                <i class="fa fa-paper-plane me-1"></i> Submit for Approval
                            </span>
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>