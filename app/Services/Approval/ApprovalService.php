<?php

use App\Services\Approval\ReferenceGenerator;
use App\Services\Approval\StepResolver;
use App\Services\Approval\UserResolver;

class ApprovalService
{

    public function __construct(

        protected StepResolver $steps,

        protected UserResolver $users,

        protected ReferenceGenerator $references

    ) {}
}
