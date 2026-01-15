<?php

namespace Modules\HRM\Livewire\HRM\Employees;

use Exception;
use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\HRM\Models\Bank;
use Modules\HRM\Models\Unit;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployeeCreate extends Component
{
    use WithFileUploads;

    // Employee identification
    public $userId;
    public $employeeId;
    public $employee;

    // Form navigation
    public $currentStep = 1;

    // Basic Information (Step 1)
    public $first_name;
    public $middle_name;
    public $last_name;
    public $dob;
    public $education;
    public $hired_date;
    public $retiring_date;
    public $phone_number;
    public $email;
    public $marital_status;
    public $gender;
    public $disability = 'no';

    // Bank Information (Step 1)
    public $bank_name;
    public $bank_account_no;

    // Employment Details (Step 2)
    public $health_insurance_no;
    public $designation;
    public $opf_number;
    public $social_security_no;
    public $zan_id_no;
    public $zan_id_file;
    public $nida_no;
    public $nida_file;
    public $unit;
    public $department;
    public $location; // Read-only, loaded from user
    public $selectedRole;
    public $is_manager = false;
    public $is_director = false;
    public $is_coordinator = false;

    // Education Levels (Step 3)
    public $education_levels = [];
    public $certificate_files = [];

    // Constants
    public $roles = ['employee', 'manager', 'director', 'director_general', 'coordinator'];
    public $educationLevels = ['certificate', 'diploma', 'advance diploma', 'bachelor', 'master', 'phd'];

    protected $listeners = ['user-selected' => 'selectUser'];

    public function mount($employeeId = null)
    {
        $this->employeeId = $employeeId;
        $this->initializeEducationLevels();

        if ($employeeId) {
            $this->loadEmployee();
        }
    }

    protected function initializeEducationLevels()
    {
        if (empty($this->education_levels)) {
            $this->education_levels = [
                ["course_name" => "", "certificate_name" => "", "holder_certificate_no" => "", "certificate_file" => ""]
            ];
        }
    }

    protected function loadEmployee()
    {
        $this->employee = Employee::with(['user.location', 'education_levels'])->where('user_id', $this->employeeId)->first();

        if (!$this->employee) {
            session()->flash('error', 'Employee not found.');
            return redirect()->route('hrm.employee.index');
        }

        $this->populateEmployeeData();
    }

    protected function populateEmployeeData()
    {
        // Basic employee data
        $this->fill([
            'nida_no' => $this->employee->nida_no,
            'dob' => $this->employee->dob,
            'hired_date' => $this->employee->hired_date,
            'retiring_date' => $this->employee->retiring_date,
            'bank_account_no' => $this->employee->bank_account_no,
            'phone_number' => $this->employee->phone_number,
            'marital_status' => $this->employee->marital_status,
            'gender' => $this->employee->gender,
            'health_insurance_no' => $this->employee->health_insurance_no,
            'designation' => $this->employee->designation,
            'opf_number' => $this->employee->opf_number,
            'social_security_no' => $this->employee->social_security_no,
            'zan_id_no' => $this->employee->zan_id_no,
            'education' => $this->employee->education,
            'disability' => $this->employee->disability ?? 'no',
            'is_manager' => $this->employee->is_manager ?? false,
            'is_director' => $this->employee->is_director ?? false,
            'is_coordinator' => $this->employee->is_coordinator ?? false,
        ]);

        // User data
        $this->userId = $this->employee->user_id;
        $this->email = $this->employee->user_id;
        $this->first_name = $this->employee->user->first_name;
        $this->middle_name = $this->employee->user->middle_name;
        $this->last_name = $this->employee->user->last_name;
        $this->location = $this->employee->user->location?->name;

        // Related data
        $this->selectedRole = $this->determineRole();
        $this->unit = $this->employee->unit_id;
        $this->bank_name = $this->employee->bank_name_id;
        $this->department = $this->employee->department_id;

        // Education levels
        $educationLevels = $this->employee->education_levels->toArray();
        $this->education_levels = !empty($educationLevels) ? $educationLevels : $this->education_levels;

        $this->loadEmail();
    }

    protected function determineRole()
    {
        // Determine role based on boolean flags
        if ($this->is_coordinator) return 'coordinator';
        if ($this->is_director) return 'director';
        if ($this->is_manager) return 'manager';
        return 'employee';
    }

    #[On('user-selected')]
    public function selectUser()
    {
        if (!$this->email) {
            return;
        }

        try {
            $user = User::with('tenant')->findOrFail($this->email);

            $this->first_name = $user->first_name;
            $this->middle_name = $user->middle_name;
            $this->last_name = $user->last_name;
            $this->userId = $user->id;
            $this->selectedLocation = $user->tenant?->name;

            $this->dispatch('setUserDetails', [
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'employeeId' => $this->userId,
                'location' => $this->location
            ]);
        } catch (Exception $e) {
            session()->flash('error', 'User not found.');
            Log::error('User Selection Error: ' . $e->getMessage());
        }
    }

    public function loadEmail()
    {
        if ($this->employee?->user) {
            $this->dispatch('eventEmail', [
                'employee_id' => $this->employee->user->id,
                'employee_role' => $this->selectedRole,
                'bank_name' => $this->employee->bank_name_id,
                'unit_id' => $this->employee->unit_id,
                'department_id' => $this->employee->department_id
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Basic Information
            'first_name' => 'required|string|min:2|max:50',
            'middle_name' => 'nullable|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'dob' => 'required|date|before:today',
            'education' => 'required|string|in:certificate,diploma,advance diploma,bachelor,master,phd',
            'hired_date' => 'required|date',
            'retiring_date' => 'nullable|date|after:hired_date',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|exists:users,id',
            'marital_status' => 'required|in:single,married,divorced',
            'gender' => 'required|in:male,female',
            'disability' => 'required|in:yes,no',

            // Bank Information
            'bank_name' => 'required|exists:banks,id',
            'bank_account_no' => 'required|string|max:50',

            // Employment Details
            'health_insurance_no' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:50',
            'opf_number' => 'nullable|string|max:50',
            'social_security_no' => 'nullable|string|max:50',
            'zan_id_no' => 'nullable|string|max:50',
            'zan_id_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'nida_no' => 'nullable|string|max:50',
            'nida_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'unit' => 'nullable|exists:units,id',
            'department' => 'nullable|exists:departments,id',
            'selectedRole' => 'required|in:' . implode(',', $this->roles),

            // Education Levels
            'education_levels.*.course_name' => 'required|string|max:100',
            'education_levels.*.certificate_name' => 'required|string|max:100',
            'education_levels.*.holder_certificate_no' => 'nullable|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Please select a user email.',
            'email.exists' => 'The selected user does not exist.',
            'dob.before' => 'Date of birth must be before today.',
            'selectedRole.required' => 'Please select a role.',
            'education.in' => 'Please select a valid education level.',
            'marital_status.in' => 'Please select a valid marital status.',
            'retiring_date.after' => 'Retiring date must be after hired date.',
        ];
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if (!$this->getErrorBag()->isEmpty()) {
            return;
        }

        $this->currentStep++;
    }

    protected function validateCurrentStep()
    {
        $rules = $this->getStepRules($this->currentStep);
        $this->validate($rules);
    }

    protected function getStepRules(int $step): array
    {
        $allRules = $this->rules();

        $stepFields = [
            1 => [
                'first_name',
                'middle_name',
                'last_name',
                'dob',
                'education',
                'hired_date',
                'retiring_date',
                'bank_name',
                'bank_account_no',
                'phone_number',
                'email',
                'marital_status',
                'gender',
                'disability'
            ],
            2 => [
                'health_insurance_no',
                'designation',
                'opf_number',
                'social_security_no',
                'zan_id_no',
                'zan_id_file',
                'nida_no',
                'nida_file',
                'unit',
                'department',
                'selectedRole'
            ],
            3 => [
                'education_levels.*.course_name',
                'education_levels.*.certificate_name',
                'education_levels.*.holder_certificate_no'
            ],
        ];

        return array_intersect_key($allRules, array_flip($stepFields[$step] ?? []));
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submitForm()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $employee = $this->saveEmployee();
            $this->saveEducationLevels($employee);

            DB::commit();

            session()->flash('success', 'Employee saved successfully!');
            $this->resetFormFields();
            $this->dispatch('resetFileState');

            return redirect()->route('hrm.employee.index');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Employee Form Submission Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            session()->flash('error', 'Failed to save employee. Please try again.');
        }
    }

    protected function saveEmployee()
    {
        // Set role-based boolean flags
        $this->setRoleFlags();

        $employeeData = [
            'nida_no' => $this->nida_no,
            'dob' => $this->dob,
            'hired_date' => $this->hired_date,
            'retiring_date' => $this->retiring_date,
            'bank_name_id' => $this->bank_name,
            'bank_account_no' => $this->bank_account_no,
            'phone_number' => $this->phone_number,
            'marital_status' => $this->marital_status,
            'gender' => $this->gender,
            'health_insurance_no' => $this->health_insurance_no,
            'designation' => $this->designation,
            'opf_number' => $this->opf_number,
            'social_security_no' => $this->social_security_no,
            'zan_id_no' => $this->zan_id_no,
            'unit_id' => $this->unit,
            'department_id' => $this->department,
            'education' => $this->education,
            'disability' => $this->disability,
            'is_active' => 'active',
            'is_manager' => $this->is_manager,
            'is_director' => $this->is_director,
            'is_coordinator' => $this->is_coordinator,
            'user_id' => $this->userId,
        ];

        // Handle file uploads
        $employeeData = $this->handleFileUploads($employeeData);

        if ($this->employeeId) {
            $employeeData['last_updated_by'] = auth()->id();
            $employee = Employee::where('user_id', $this->employeeId)->first();
            $employee->update($employeeData);
        } else {
            $employeeData['created_by'] = auth()->id();
            $employee = Employee::create($employeeData);
        }

        return $employee;
    }

    protected function setRoleFlags()
    {
        // Reset all flags
        $this->is_manager = false;
        $this->is_director = false;
        $this->is_coordinator = false;

        // Set flag based on selected role
        switch ($this->selectedRole) {
            case 'manager':
                $this->is_manager = true;
                break;
            case 'director':
            case 'director_general':
                $this->is_director = true;
                break;
            case 'coordinator':
                $this->is_coordinator = true;
                break;
        }
    }

    protected function handleFileUploads(array $employeeData): array
    {
        // Handle NIDA file
        if ($this->nida_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->employeeId && $this->employee?->nida_file) {
                Storage::disk('public')->delete($this->employee->nida_file);
            }
            $employeeData['nida_file'] = $this->nida_file->store('nida', 'public');
        }

        // Handle Zanzibar ID file
        if ($this->zan_id_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->employeeId && $this->employee?->zan_id_file) {
                Storage::disk('public')->delete($this->employee->zan_id_file);
            }
            $employeeData['zan_id_file'] = $this->zan_id_file->store('zan_id', 'public');
        }

        return $employeeData;
    }

    protected function saveEducationLevels($employee)
    {
        // Delete existing education levels if updating
        $employee->education_levels()->delete();

        foreach ($this->education_levels as $index => $edu) {
            $data = [
                'course_name' => $edu['course_name'],
                'certificate_name' => $edu['certificate_name'],
                'holder_certificate_no' => $edu['holder_certificate_no'] ?? null,
            ];

            // Handle certificate file upload
            if (!empty($this->certificate_files[$index])) {
                $data['certificate_file'] = $this->certificate_files[$index]->store('certificates', 'public');
            }

            $employee->education_levels()->create($data);
        }
    }

    public function resetFormFields()
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'dob',
            'education',
            'hired_date',
            'retiring_date',
            'bank_name',
            'bank_account_no',
            'phone_number',
            'email',
            'marital_status',
            'gender',
            'health_insurance_no',
            'designation',
            'opf_number',
            'social_security_no',
            'zan_id_no',
            'zan_id_file',
            'nida_no',
            'nida_file',
            'unit',
            'department',
            'selectedRole',
            'userId',
            'certificate_files',
            'location',
            'disability',
            'is_manager',
            'is_director',
            'is_coordinator'
        ]);

        $this->currentStep = 1;
        $this->initializeEducationLevels();
    }

    public function addEducationLevel()
    {
        $this->education_levels[] = [
            "course_name" => "",
            "certificate_name" => "",
            "holder_certificate_no" => "",
            "certificate_file" => ""
        ];
    }

    public function removeEducationLevel($index)
    {
        if (count($this->education_levels) > 1) {
            unset($this->education_levels[$index]);
            $this->education_levels = array_values($this->education_levels);
        }
    }

    public function render()
    {
        $emails = $this->getAvailableEmails();

        return view('hrm::livewire.h-r-m.employees.employee-create', [
            'banks' => Bank::pluck('slug', 'id'),
            'locations' => Tenant::pluck('name', 'id'),
            'departments' => Department::pluck('name', 'id'),
            'units' => Unit::pluck('name', 'id'),
            'emails' => $emails,
            'educationLevels' => $this->educationLevels,
        ]);
    }

    protected function getAvailableEmails()
    {
        $query = User::query();

        if (!$this->employeeId) {
            $query->doesntHave('employee');
        }

        return $query->orderBy('created_at', 'desc')->pluck('email', 'id');
    }
}
