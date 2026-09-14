{{-- resources/views/livewire/approvals/chain-manager.blade.php --}}
<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Approval Chains</h5>
        <button wire:click="$set('showForm', true)" class="btn btn-primary btn-sm">
            + New Chain
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Name</th>
                    <th>Module</th>
                    <th>Steps</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($chains as $chain)
                    <tr wire:key="chain-{{ $chain->id }}">
                        <td class="font-weight-medium">{{ $chain->name }}</td>
                        <td class="text-muted">{{ $chain->module ?? '—' }}</td>
                        <td class="text-muted">{{ $chain->steps_count }}</td>
                        <td>
                            <span class="badge {{ $chain->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $chain->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <button wire:click="toggleActive({{ $chain->id }})" class="btn btn-link btn-sm p-0 mr-3">
                                Toggle
                            </button>
                            <a href="{{ route('approvals.chains.steps', $chain) }}" wire:navigate
                                class="btn btn-link btn-sm p-0">
                                Manage Steps
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No approval chains yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Bootstrap 4 Modal --}}
    @if ($showForm)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);"
            wire:click.self="$set('showForm', false)">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="createChain">
                        <div class="modal-header">
                            <h5 class="modal-title">New Approval Chain</h5>
                            <button type="button" class="close" wire:click="$set('showForm', false)">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" wire:model="name"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label>Module</label>
                                <input type="text" wire:model="module" placeholder="e.g. payroll, leave"
                                    class="form-control">
                            </div>

                            <div class="form-check">
                                <input type="checkbox" wire:model="is_active" class="form-check-input" id="isActiveCheck">
                                <label class="form-check-label" for="isActiveCheck">Active</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="$set('showForm', false)"
                                class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>