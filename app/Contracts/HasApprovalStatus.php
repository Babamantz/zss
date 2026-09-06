<?php

namespace App\Contracts;

interface HasApprovalStatus
{
    public function onApprovalCompleted(): void;
    public function onApprovalRejected(): void;
}
