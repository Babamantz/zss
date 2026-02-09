<?php

namespace Modules\HRM\Livewire\HRM\Employees;


use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployeeEdit extends Component
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
    public $photo_file;
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

    public function mount($id)
    {
        $this->employeeId = $id;

        $employee = Employee::findOrFail($this->employeeId);

        $this->populateEmploye($employee);
    }

    protected function populateEmploye($employee)
    {

        // Basic employee data
        $this->fill([
            'nida_no' => $employee->nida_no,
            'dob' => $employee->dob,
            'hired_date' => $employee->hired_date,
            'retiring_date' => $employee->retiring_date,
            'bank_account_no' => $employee->bank_account_no,
            'phone_number' => $employee->phone_number,
            'marital_status' => $employee->marital_status,
            'gender' => $employee->gender,
            'health_insurance_no' => $employee->health_insurance_no,
            'designation' => $employee->designation,
            'opf_number' => $employee->opf_number,
            'social_security_no' => $employee->social_security_no,
            'zan_id_no' => $employee->zan_id_no,
            'education' => $employee->education,
            'disability' => $employee->disability ?? 'no',
            'is_manager' => $employee->is_manager ?? false,
            'is_director' => $employee->is_director ?? false,
            'is_coordinator' => $employee->is_coordinator ?? false,
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
        $this->education_levels = !empty($educationLevels) ? $educationLevels : $this->education_level;
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

            return redirect()->route('hrm.employees.create');
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
        // Handle Photo  file
        if ($this->photo_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->employeeId && $this->employee?->photo_file) {
                Storage::disk('public')->delete($this->employee->photo_file);
            }
            $employeeData['photo_file'] = $this->photo_file->store('photo', 'public');
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


    public function render()
    {
        return view('hrm::livewire.h-r-m.employees.employee-edit');
    }
}
