{{-- resources/views/livewire/approvals/transaction-show.blade.php --}}
<div class="space-y-6">
    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('success') }}</div>
    @endif

    <div>
        <h2 class="text-lg font-semibold">
            {{ class_basename($transaction->approvable_type) }} #{{ $transaction->approvable_id }}
        </h2>
        <p class="text-sm text-gray-500">Chain: {{ $transaction->chain->name }} · Status:
            {{ ucfirst($transaction->status->value) }}</p>
    </div>

    {{-- step progress --}}
    <div class="flex gap-2">
        @foreach ($transaction->chain->steps as $step)
            @php
                $isPast = $transaction->currentStep && $step->order < $transaction->currentStep->order;
                $isCurrent = $transaction->current_step_id === $step->id;
                $isDoneOverall = $transaction->status->value === 'approved';
            @endphp
            <div @class([
                'px-3 py-2 rounded text-sm border',
                'bg-green-100 border-green-300' => $isPast || $isDoneOverall,
                'bg-blue-100 border-blue-400 font-semibold' => $isCurrent,
                'bg-gray-100 border-gray-200' => !$isPast && !$isCurrent && !$isDoneOverall,
            ])>
                {{ $step->name }}
            </div>
        @endforeach
    </div>

    {{-- action timeline --}}
    <div class="border rounded divide-y">
        @forelse ($transaction->actions as $action)
            <div class="p-4" wire:key="action-{{ $action->id }}">
                <p class="font-medium">
                    {{ $action->actionedBy->name }}
                    <span @class(['text-sm', 'text-green-600' => $action->decision->value === 'approved', 'text-red-600' => $action->decision->value === 'rejected'])>
                        {{ ucfirst($action->decision->value) }}
                    </span>
                </p>
                @if ($action->remarks)
                    <p class="text-sm text-gray-600">{{ $action->remarks }}</p>
                @endif
                <p class="text-xs text-gray-400">{{ $action->created_at->format('d M Y H:i') }}</p>
            </div>
        @empty
            <p class="p-4 text-gray-500">No actions yet.</p>
        @endforelse
    </div>

    {{-- approve/reject panel --}}
    @if ($transaction->canBeActionedBy(auth()->user()))
        <div class="border rounded p-4 bg-gray-50 space-y-3">
            <textarea wire:model="remarks" placeholder="Remarks (required for rejection)"
                class="w-full border rounded px-3 py-2"></textarea>
            @error('remarks') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <div class="flex gap-2">
                <button wire:click="approve" wire:confirm="Approve this step?" class="btn btn-success">Approve</button>
                <button wire:click="reject" wire:confirm="Reject this transaction?" class="btn btn-danger">Reject</button>
            </div>
        </div>
    @endif
</div>