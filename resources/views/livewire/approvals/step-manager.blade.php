{{-- resources/views/livewire/approvals/step-manager.blade.php --}}
<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Steps for: {{ $chain->name }}</h5>
        <button wire:click="$set('showForm', true)" class="btn btn-primary btn-sm">
            + Add Step
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th style="width: 60px;">Order</th>
                    <th>Step Name</th>
                    <th>Approver</th>
                    <th>Requires All</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($steps as $step)
                    <tr wire:key="step-{{ $step->id }}">
                        <td class="text-muted">#{{ $step->order }}</td>
                        <td class="font-weight-medium">{{ $step->name }}</td>
                        <td class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', $step->approver_type->value)) }}: {{ $step->approver_value }}
                        </td>
                        <td>
                            @if ($step->requires_all)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button wire:click="editStep({{ $step->id }})" class="btn btn-link btn-sm p-0 mr-3">
                                Edit
                            </button>
                            <button wire:click="deleteStep({{ $step->id }})" wire:confirm="Remove this step?"
                                class="btn btn-link btn-sm p-0 text-danger">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No steps defined yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Bootstrap 4 Modal — shared by Add and Edit --}}
    @if ($showForm)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);"
            wire:click.self="cancelForm">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form wire:submit.prevent="{{ $editingStepId ? 'updateStep' : 'addStep' }}">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingStepId ? 'Edit Approval Step' : 'Add Approval Step' }}
                            </h5>
                            <button type="button" class="close" wire:click="cancelForm">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Order</label>
                                    <input type="number" wire:model="order"
                                        class="form-control @error('order') is-invalid @enderror">
                                    @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Step Name</label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label>Approver Type</label>
                                    <select wire:model="approver_type" class="form-control">
                                        <option value="role">Role</option>
                                        <option value="user">Specific User</option>
                                        <option value="department_head">Department Head</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-7">
                                    <label>Approver Value</label>
                                    <input type="text" wire:model="approver_value"
                                        placeholder="role name / user id / department id"
                                        class="form-control @error('approver_value') is-invalid @enderror">
                                    @error('approver_value')
                                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" wire:model="requires_all" class="form-check-input"
                                    id="requiresAllCheck">
                                <label class="form-check-label" for="requiresAllCheck">
                                    Requires all eligible approvers to approve
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="cancelForm" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                {{ $editingStepId ? 'Update Step' : 'Add Step' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>