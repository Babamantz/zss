<?php

namespace App\Traits;

trait HasApprovableWorkFlow
{
    //

    public function approvalRequest()
    {
        return $this->morphOne(
            ApprovalRequest::class,
            'approvable'
        );
    }

    public function submitForApproval(
        ?string $title = null,
        ?string $description = null
    ) {
        return app(ApprovalService::class)
            ->submit(
                $this,
                $title,
                $description
            );
    }

    public function approve(
        ?string $comments = null
    ) {
        return app(ApprovalService::class)
            ->approve(
                $this->approvalRequest,
                auth()->user(),
                $comments
            );
    }

    public function reject(
        string $reason
    ) {
        return app(ApprovalService::class)
            ->reject(
                $this->approvalRequest,
                auth()->user(),
                $reason
            );
    }
}
