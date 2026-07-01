<?php

namespace Modules\PAYROLL\Livewire\Payroll\FinanceProfile;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HRM\Models\Employee;
use Modules\PAYROLL\Models\EmployeeFinanceProfile;

class FinanceProfileIndex extends Component
{

    use WithPagination;

    // ── Filter / Search ───────────────────────────────────────────────────────
    public string $search      = '';
    public string $sortField   = 'created_at';
    public string $sortDir     = 'desc';

    // ── Modal state ───────────────────────────────────────────────────────────
    public bool  $showModal  = false;
    public bool  $showView   = false;
    public ?int  $editId     = null;   // EmployeeFinanceProfile id
    public ?int  $viewId     = null;

    // ── Form fields ───────────────────────────────────────────────────────────
    public ?int   $employee_id          = null;
    public string $base_salary          = '';
    public string $bank_account_number  = '';
    public string $tax_id               = '';

    // ── Computed for view modal ───────────────────────────────────────────────
    public ?EmployeeFinanceProfile $viewProfile = null;

    protected function rules(): array
    {
        $uniqueEmployee = 'unique:employee_finance_profiles,employee_id';

        // On edit, ignore the current record's employee_id uniqueness
        if ($this->editId) {
            $uniqueEmployee .= ',' . $this->editId;
        }

        return [
            'employee_id'         => "required|integer|exists:employees,id|{$uniqueEmployee}",
            'base_salary'         => 'required|numeric|min:0',
            'bank_account_number' => 'required|string|max:50',
            'tax_id'              => 'nullable|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'employee_id.required'         => 'Please select an employee.',
            'employee_id.unique'           => 'This employee already has a finance profile.',
            'base_salary.required'         => 'Base salary is required.',
            'base_salary.numeric'          => 'Base salary must be a valid number.',
            'bank_account_number.required' => 'Bank account number is required.',
        ];
    }

    // ── Sorting ───────────────────────────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'asc';
        }
    }

    // ── Modal controls ────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $profileId): void
    {
        $profile = EmployeeFinanceProfile::findOrFail($profileId);

        $this->editId               = $profile->id;
        $this->employee_id          = $profile->employee_id;
        $this->base_salary          = $profile->base_salary;
        $this->tax_id               = $profile->tax_id ?? '';
        $this->showModal            = true;
    }



    public function updatedEmployeeId(int $value)
    {
        if (!$value) {
            $this->bank_account_number = '';
            return;
        }

        $employee = Employee::with('bankAccount')->find($value);

        if ($employee && $employee->bankAccount) {
            $this->bank_account_number = $employee->bankAccount->account_no;
        } else {
            $this->bank_account_number = $employee->bankAccount?->account_no ?? null;
        }
    }




    public function openView(int $profileId): void
    {
        $this->viewProfile = EmployeeFinanceProfile::with([
            'employee.user',
            'employee.department',
            'employee.unit',
            'employee.components.component',
        ])->findOrFail($profileId);

        $this->showView = true;
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal  = false;
        $this->showView   = false;
        $this->viewProfile = null;
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $data = [
                'employee_id'         => $this->employee_id,
                'base_salary'         => $this->base_salary,
                'tax_id'              => $this->tax_id ?: null,
            ];

            if ($this->editId) {
                EmployeeFinanceProfile::findOrFail($this->editId)->update($data);
                $message = 'Finance profile updated successfully.';
            } else {
                EmployeeFinanceProfile::create($data);
                $message = 'Finance profile created successfully.';
            }

            DB::commit();

            $this->resetForm();
            $this->showModal = false;
            session()->flash('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('FinanceProfile save error', [
                'error'   => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            session()->flash('error', 'Failed to save finance profile. Please try again.');
        }
    }

    // ── Delete (soft) ─────────────────────────────────────────────────────────

    public function delete(int $profileId): void
    {
        try {
            EmployeeFinanceProfile::findOrFail($profileId)->delete();
            session()->flash('success', 'Finance profile removed.');
        } catch (Exception $e) {
            Log::error('FinanceProfile delete error', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to remove finance profile.');
        }
    }

    protected function resetForm(): void
    {
        $this->reset([
            'editId',
            'employee_id',
            'base_salary',
            'tax_id',
        ]);
        $this->resetErrorBag();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        // Employees who do NOT yet have a profile (for create dropdown)
        // On edit we also include the current employee so it appears selected
        $availableEmployees = Employee::with('user')
            ->where('is_active', 'active')
            ->when(
                !$this->editId,
                fn($q) => $q->doesntHave('financeProfile')
            )
            ->get();

        // Profiles list
        $profiles = EmployeeFinanceProfile::with(['employee.user', 'employee.department', 'employee.bankAccount'])
            // ->when($this->search, function ($q) {
            //     $q->whereHas(
            //         'employee.user',
            //         fn($u) =>
            //         $u->where('first_name', 'like', "%{$this->search}%")
            //             ->orWhere('last_name',  'like', "%{$this->search}%")
            //     );
            //     $q->whereHas('bankAccount', function ($q) {
            //         $q->orWhere('account_no', "%{$this->search}%");
            //     })
            //         ->orWhere('bankAccount', 'like', "%{$this->search}%")
            //         ->orWhere('tax_id', 'like', "%{$this->search}%");
            // })
            // ->orderBy($this->sortField, $this->sortDir)
            // ->paginate(15);

            ->when($this->search, function ($query) {
                $query->where(function ($q) {

                    $q->whereHas('employee.user', function ($u) {
                        $u->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    })

                        ->orWhereHas('employee.bankAccount', function ($b) {
                            $b->where('account_no', 'like', "%{$this->search}%");
                        })

                        ->orWhere('tax_id', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);

        // Summary stats
        $stats = [
            'total'      => EmployeeFinanceProfile::count(),
            'avg_salary' => EmployeeFinanceProfile::avg('base_salary') ?? 0,
            'max_salary' => EmployeeFinanceProfile::max('base_salary') ?? 0,
            'min_salary' => EmployeeFinanceProfile::min('base_salary') ?? 0,
            'no_profile' => Employee::where('is_active', 'active')
                ->doesntHave('financeProfile')->count(),
        ];


        return view(
            'payroll::livewire.payroll.finance-profile.finance-profile-index',
            compact('profiles', 'availableEmployees', 'stats')
        );
    }
}
