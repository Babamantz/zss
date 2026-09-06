@if($model->approvalsPaused !== true)
    {{-- ── Approvals Card ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-1">{{ __('ringlesoft::approvals.approvals') }}</h6>
                    <small class="text-muted">{{ __('ringlesoft::approvals.by') }} /
                        {{ __('ringlesoft::approvals.date') }}</small>
                </div>
            </div>

            @if($model->isSubmitted())
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">{{ __('ringlesoft::approvals.by') }}</th>
                                @foreach($modelApprovalSteps as $step)
                                    <th>{{ $step->role?->name ?? __('ringlesoft::approvals.step') . ' ' . $loop->iteration }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-muted small">{{ __('ringlesoft::approvals.date') }}</td>
                                @foreach($modelApprovalSteps as $step)
                                    <td>
                                        @if($currentApproval = $step->approval)
                                            <div class="d-inline-block position-relative" data-bs-toggle="tooltip"
                                                title="{{ $currentApproval->comment ?? __('ringlesoft::approvals.no_comment') . '!' }}">
                                                @if($currentApproval->approval_action === \RingleSoft\LaravelProcessApproval\Enums\ApprovalActionEnum::APPROVED->value)
                                                    @if($signature = $currentApproval->getSignature())
                                                        <img src="{{ $signature }}" style="max-height: 40px;"
                                                            alt="{{ __('ringlesoft::approvals.signature') }}">
                                                    @else
                                                        <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center"
                                                            style="width: 36px; height: 36px;">
                                                            <i class="fa fa-check text-success"></i>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center"
                                                        style="width: 36px; height: 36px;">
                                                        @if($currentApproval->approval_action === \RingleSoft\LaravelProcessApproval\Enums\ApprovalStatusEnum::OVERRIDDEN->value)
                                                            <i class="fa fa-circle-exclamation text-danger"></i>
                                                        @else
                                                            <i class="fa fa-xmark text-danger"></i>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="small mt-1">{{ $currentApproval->user?->name }}</div>
                                                <div class="text-muted" style="font-size: 12px;">
                                                    {{ $currentApproval->created_at->format('d F, Y') }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($model->approvalsPaused !== 'ONLY_ACTIONS')
                    <div class="approval-actions">
                        @if($nextApprovalStep && $model->approvalsDisabled !== 'ONLY_ACTIONS')
                            @if($userCanApprove)
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="text-muted small">
                                        @if($model->isRejected())
                                            {{ __('ringlesoft::approvals.request_rejected_re_approve') }}
                                            <strong>{{ $nextApprovalStep->role->name }}</strong>
                                        @elseif($model->isReturned())
                                            {{ __('ringlesoft::approvals.request_returned_re_approve') }}
                                            <strong>{{ $nextApprovalStep->role->name }}</strong>
                                        @elseif($model->isDiscarded())
                                            {{ __('ringlesoft::approvals.request_was_discarded') }}
                                        @else
                                            {{ __('ringlesoft::approvals.you_can_approve_this') }}
                                            <strong>{{ $nextApprovalStep->role->name }}</strong>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($model->isRejected())
                                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#discard-modal">
                                                <i class="fa fa-ban me-1"></i>{{ __('ringlesoft::approvals.discard') }}
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#reject-modal">
                                                <i class="fa fa-xmark me-1"></i>{{ __('ringlesoft::approvals.reject') }}
                                            </button>
                                        @endif
                                        @if(!$model->isRejected())
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#return-modal">
                                                <i class="fa fa-rotate-left me-1"></i>{{ __('ringlesoft::approvals.return') }}
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#approve-modal">
                                            <i class="fa fa-check me-1"></i>
                                            {{ $model->isRejected() ? __('ringlesoft::approvals.re_approve') : ucfirst(__('ringlesoft::approvals.' . strtolower($nextApprovalStep->action)) ?? __('ringlesoft::approvals.approve')) }}
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="text-end text-muted small">
                                    {{ __('ringlesoft::approvals.waiting_for_approval_from') }}
                                    <strong>{{ $nextApprovalStep->role->name }}</strong>
                                </div>
                            @endif
                        @else
                            <div class="alert {{ $model->isDiscarded() ? 'alert-secondary' : 'alert-success' }} mb-0 py-2">
                                @if($model->isDiscarded())
                                    <i class="fa fa-ban me-1"></i>{{ __('ringlesoft::approvals.discarded') }}!
                                @else
                                    <i class="fa fa-circle-check me-1"></i>{{ __('ringlesoft::approvals.approval_completed') }}!
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ── Modals ──────────────────────────────────────────────────────────── --}}
    @if($model->canBeApprovedBy(auth()?->user()))
        {{-- Approve Modal --}}
        <div class="modal fade" id="approve-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="{{ route('ringlesoft.process-approval.approve', $model) }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()?->id() }}">
                        <input type="hidden" name="model_name" value="{{ $model->getApprovableType() }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('ringlesoft::approvals.approve_request') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="comment" class="form-control" rows="3"
                                placeholder="{{ __('ringlesoft::approvals.write_comment') }} ({{ __('ringlesoft::approvals.optional') }})"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                {{ __('ringlesoft::approvals.no_cancel') }}
                            </button>
                            <button type="submit" class="btn btn-success btn-sm">
                                {{ ucfirst(__('ringlesoft::approvals.' . strtolower($nextApprovalStep->action ?? 'Approve'))) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="reject-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="{{ route('ringlesoft.process-approval.reject', $model) }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()?->id() }}">
                        <input type="hidden" name="model_name" value="{{ $model->getApprovableType() }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('ringlesoft::approvals.reject_request') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="comment" class="form-control" rows="3" required
                                placeholder="{{ __('ringlesoft::approvals.write_comment') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                {{ __('ringlesoft::approvals.no_cancel') }}
                            </button>
                            <button type="submit" class="btn btn-danger btn-sm">
                                {{ __('ringlesoft::approvals.reject') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Return Modal --}}
        <div class="modal fade" id="return-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="{{ route('ringlesoft.process-approval.return', $model) }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()?->id() }}">
                        <input type="hidden" name="model_name" value="{{ $model->getApprovableType() }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('ringlesoft::approvals.return_request') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="comment" class="form-control" rows="3" required
                                placeholder="{{ __('ringlesoft::approvals.write_comment') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                {{ __('ringlesoft::approvals.no_cancel') }}
                            </button>
                            <button type="submit" class="btn btn-secondary btn-sm">
                                {{ __('ringlesoft::approvals.return') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Discard Modal --}}
        <div class="modal fade" id="discard-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="{{ route('ringlesoft.process-approval.discard', $model) }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()?->id() }}">
                        <input type="hidden" name="model_name" value="{{ $model->getApprovableType() }}">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('ringlesoft::approvals.discard_request') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="comment" class="form-control" rows="3" required
                                placeholder="{{ __('ringlesoft::approvals.write_comment') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                {{ __('ringlesoft::approvals.no_cancel') }}
                            </button>
                            <button type="submit" class="btn btn-danger btn-sm">
                                {{ __('ringlesoft::approvals.discard') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif