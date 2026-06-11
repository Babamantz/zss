<?php

namespace Modules\HRM\Livewire\HRM\Employees;

use App\Models\Certificate;
use App\Models\Identification;
use App\Models\Tenant;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\HRM\Models\Bank;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Unit;

class EmployeeCreate extends Component
{
    use WithFileUploads;
    // Employee identification
    public $userId;
    public $employeeId;
    public $mode;
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
    public $contacts = [
        ['type' => 'personal',    'phone_number' => ''],
        ['type' => 'next_of_kin', 'phone_number' => ''],
    ];
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
  
    public $existing_zan_id_file; // For preview
    public $birth_certificate_file;
    public $existing_birth_certificate_file;
    public $employment_contract_file;
    public $existing_employment_contract_file;  //For preview
    public $existing_nida_file; // For preview
    public $photo_file;
    public $existing_photo_file; // For preview
    public $unit;
    public $department;
    public $location;
    public $selectedRole = '';
    public $is_officer = false;
  
    // Education Levels (Step 3)
    public $education_levels = [];
    public $identification_files = [];

    public $certificate_files = [];
    public $existing_certificate_files = []; // For preview
    // Edit mode flag
    public $isEditMode = false;
    public $isViewMode = false;
    // Constants
    public $roles = ['officer', 'manager', 'director', 'director_general', 'coordinator'];
    public $educationLevels = ['certificate', 'form-iv', 'diploma', 'advance_diploma', 'bachelor', 'master', 'phd'];

    protected $listeners = ['user-selected' => 'selectUser'];
    public $identification_items = [];       // rows bound to Alpine
    public $identification_upload_files = []; // actual UploadedFile objects

    // Certificates (Step 5  — replaces old flat fields)
    public $certificate_items = [];
    public $certificate_upload_files = [];

    public function mount($employeeId = null, $mode = null)
    {
        $this->employeeId = $employeeId;
        $this->isEditMode = !is_null($employeeId);
        $this->isViewMode = $mode == 'view';
        $this->initializeEducationLevels();
        $this->initializeIdentificationItems();
        $this->initializeCertificateItems();
        $this->contacts = [
            ['type' => 'personal',    'phone_number' => ''],
            ['type' => 'next_of_kin', 'phone_number' => ''],
        ];
        // $this->initializeRoleFlags();

        if ($employeeId) {
            $this->loadEmployee();
        }
    }

    // ── MOUNT: initialise the new collections ────────────────────────────────

    protected function initializeIdentificationItems(): void
    {
        if (empty($this->identification_items)) {
            $this->identification_items = [
                ['identification_id' => '', 'identification_no' => '', 'existing_file' => null],
            ];
        }
    }

    protected function initializeCertificateItems(): void
    {
        if (empty($this->certificate_items)) {
            $this->certificate_items = [
                ['certificate_id' => '', 'certificate_no' => '', 'existing_file' => null],
            ];
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
        $this->employee = Employee::with([
            'user',
            'user.tenant',
            'identifications',
            'certificates',
            'education_levels'
        ])->where('id', $this->employeeId)->first();
        //  dd($this->employee);
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
            'dob' => $this->employee->dob,
            'hired_date' => $this->employee->hired_date,
            'retiring_date' => $this->employee->retiring_date,
            'bank_account_no' => $this->employee->bank_account_no,
            'marital_status' => $this->employee->marital_status,
            'gender' => $this->employee->gender,
            'health_insurance_no' => $this->employee->health_insurance_no,
            'designation' => $this->employee->designation,
            'opf_number' => $this->employee->opf_number,
            'social_security_no' => $this->employee->social_security_no,
            'education' => $this->employee->education,
            'disability' => $this->employee->disability ?? 'no',

            'is_officer' => $this->employee->is_officer ?? false,
        ]);
        // Store existing file paths for preview
   
        $this->existing_photo_file = $this->employee->photo_file;
        $this->existing_birth_certificate_file = $this->employee->birth_certificate_file;
        $this->existing_employment_contract_file = $this->employee->employment_contract_file;
        // User data
        $this->userId = $this->employee->user_id;
        $this->email = $this->employee->user_id;
        $this->first_name = $this->employee->user->first_name;
        $this->middle_name = $this->employee->user->middle_name;
        $this->last_name = $this->employee->user->last_name;
        $this->location = $this->employee->user->location?->name;
        // Related data
        $this->initializeRoleFlags();
        $this->unit = $this->employee->unit_id;
        $this->bank_name = $this->employee->bank_name_id;
        $this->department = $this->employee->department_id;

        // Education levels with existing files
        $educationLevels = $this->employee->education_levels->map(function ($edu) {
            return [
                'id' => $edu->id,
                'course_name' => $edu->course_name,
                'certificate_name' => $edu->certificate_name,
                'holder_certificate_no' => $edu->holder_certificate_no,
                'certificate_file' => $edu->certificate_file,
                'existing_file' => $edu->certificate_file, // For preview
            ];
        })->toArray();
        $this->identification_items = $this->employee->identifications
            ->map(fn($i) => [
                'identification_id' => $i->identification_id,
                'identification_no'  => $i->identification_no,
                'existing_file'      => $i->identification_path,
            ])->toArray() ?: $this->identification_items;

        // Certificates
        $this->certificate_items = $this->employee->certificates
            ->map(fn($c) => [
                'certificate_id' => $c->certificate_id,
                'certificate_no'  => $c->certificate_no,
                'existing_file'   => $c->certificate_path,
            ])->toArray() ?: $this->certificate_items;

        // Contacts
        $saved = json_decode($this->employee->contacts, true);
        if (!empty($saved)) {
            $this->contacts = $saved;
        }

        $this->education_levels = !empty($educationLevels) ? $educationLevels : $this->education_levels;

        $this->loadEmail();
    }

    // Download file method
    public function downloadFile($type, $index = null)
    {
        try {
            $filePath = null;

            switch ($type) {
                case 'nida':
                    $filePath = $this->existing_nida_file;
                    break;
                case 'zan_id':
                    $filePath = $this->existing_zan_id_file;
                    break;
                case 'photo':
                    $filePath = $this->existing_photo_file;
                    break;
                case 'birth':
                    $filePath = $this->existing_birth_certificate_file;
                    break;
                case 'contract':
                    $filePath = $this->existing_employment_contract_file;
                    break;
                case 'certificate':
                    if (isset($this->education_levels[$index]['existing_file'])) {
                        $filePath = $this->education_levels[$index]['existing_file'];
                    }
                    break;
            }

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath);
            }

            session()->flash('error', 'File not found.');
        } catch (Exception $e) {
            Log::error('File Download Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to download file.');
        }
    }

    // Remove existing file
    public function removeExistingFile($type, $index = null)
    {
        try {
            switch ($type) {
                case 'nida':
                    if ($this->existing_nida_file && Storage::disk('public')->exists($this->existing_nida_file)) {
                        Storage::disk('public')->delete($this->existing_nida_file);
                    }
                    $this->existing_nida_file = null;
                    break;
                case 'zan_id':
                    if ($this->existing_zan_id_file && Storage::disk('public')->exists($this->existing_zan_id_file)) {
                        Storage::disk('public')->delete($this->existing_zan_id_file);
                    }
                    $this->existing_zan_id_file = null;
                    break;
                case 'photo':
                    if ($this->existing_photo_file && Storage::disk('public')->exists($this->existing_photo_file)) {
                        Storage::disk('public')->delete($this->existing_photo_file);
                    }
                    $this->existing_photo_file = null;
                    break;
                case 'birth':
                    if ($this->existing_birth_certificate_file && Storage::disk('public')->exists($this->existing_birth_certificate_file)) {
                        Storage::disk('public')->delete($this->existing_birth_certificate_file);
                    }
                    $this->existing_birth_certificate_file = null;
                    break;
                case 'contract':
                    if ($this->existing_employment_contract_file && Storage::disk('public')->exists($this->existing_employment_contract_file)) {
                        Storage::disk('public')->delete($this->existing_employment_contract_file);
                    }
                    $this->existing_employment_contract_file = null;
                    break;
                case 'certificate':
                    if (isset($this->education_levels[$index]['existing_file'])) {
                        $filePath = $this->education_levels[$index]['existing_file'];
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $this->education_levels[$index]['existing_file'] = null;
                    }
                    break;
            }

            session()->flash('success', 'File removed successfully.');
        } catch (Exception $e) {
            Log::error('File Removal Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to remove file.');
        }
    }

    // Helper method to get file preview URL
    public function getFileUrl($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->url($filePath);
        }
        return null;
    }

    // Helper method to check if file is image
    public function isImageFile($filePath)
    {
        if (!$filePath) return false;

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
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
            $this->location = $user->tenant?->name;

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
                // 'employee_role' => $this->selectedRole,
                // 'bank_name' => $this->employee->bank_name_id,
                // 'unit_id' => $this->employee->unit_id,
                // 'department_id' => $this->employee->department_id
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
            'email' => 'required|exists:users,id',
            // Identifications
            'identification_items.*.identification_id' => 'required|exists:identifications,id',
            'identification_items.*.identification_no'  => 'nullable|string|max:60',

            // Certificates
            'certificate_items.*.certificate_id' => 'required|exists:certificates,id',
            'certificate_items.*.certificate_no' => 'nullable|string|max:60',

            // Contacts
            'contacts.0.phone_number' => 'required|string|max:15',  // personal — required
            'contacts.1.phone_number' => 'nullable|string|max:15',

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
            'photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
                'email',
                'marital_status',
                'gender',
                'disability',
                'contacts.0.phone_number',
                'contacts.1.phone_number'
            ],
            2 => [
                'health_insurance_no',
                'designation',
                'opf_number',
                'social_security_no',
                'unit',
                'department',
                'selectedRole',
            ],
            3 => [
                'identification_items.*.identification_id',
                'identification_items.*.identification_no',
            ],
            4 => [
                'certificate_items.*.certificate_id',
                'certificate_items.*.certificate_no',
            ],

            5 => [
                'education_levels.*.course_name',
                'education_levels.*.certificate_name',
                'education_levels.*.holder_certificate_no',
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
            $this->saveIdentifications($employee);
            $this->saveCertificates($employee);

            DB::commit();

            $message = $this->isEditMode ? 'Employee updated successfully!' : 'Employee created successfully!';
            session()->flash('success', $message);

            if (!$this->isEditMode) {
                $this->resetFormFields();
                $this->dispatch('resetFileState');
            }

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
        // $this->setRoleFlags();

        $employeeData = [
            'dob' => $this->dob,
            'hired_date' => $this->hired_date,
            'retiring_date' => $this->retiring_date,
            'bank_name_id' => $this->bank_name,
            'bank_account_no' => $this->bank_account_no,
            'marital_status' => $this->marital_status,
            'gender' => $this->gender,
            'health_insurance_no' => $this->health_insurance_no,
            'designation' => $this->designation,
            'opf_number' => $this->opf_number,
            'social_security_no' => $this->social_security_no,
            'hr_registered' => true,
          
            'is_officer' => $this->is_officer,
            'is_director' => $this->is_director,
            'unit_id' => $this->unit,
            'department_id' => $this->department,
            'education' => $this->education,
            'disability' => $this->disability,
            'is_active' => 'active',
            'user_id' => $this->userId, // ── IN saveEmployee(): store contacts as JSON ─────────────────────────────
            // Add this to $employeeData array:
            'contacts' => json_encode(array_filter($this->contacts, fn($c) => filled($c['phone_number']))),
        ];

        // dd($this->is_manager);
        $employeeData = $this->handleFileUploads($employeeData);

        if ($this->employeeId) {
            $employeeData['last_updated_by'] = auth()->id();
            $employee = Employee::where('id', $this->employeeId)->first();
            $employee->update($employeeData);
        } else {
            $employeeData['created_by'] = auth()->id();
            $employee = Employee::create($employeeData);
        }

        return $employee;
    }

    // protected function setRoleFlags()
    // {
    //     $this->is_officer = false;

    //     // Set the selected role
    //     // dd($this->selectedRole);
    //     switch ($this->selectedRole) {
    //         case 'officer':
    //             $this->is_officer = true;
    //             break;

    //         case 'manager':
    //             $this->is_manager = true;
    //             break;

    //         case 'director':
    //             $this->is_director = true;
    //             break;

    //         case 'director_general':
    //             $this->is_director_general = true;
    //             break;

    //         case 'coordinator':
    //             $this->is_coordinator = true;
    //             break;
    //     }
    // }

    // protected function initializeRoleFlags()
    // {
    //     // Determine selected role from flags
    //     if ($this->is_director_general) {
    //         $this->selectedRole = 'director_general';
    //     } elseif ($this->is_director) {
    //         $this->selectedRole = 'director';
    //     } elseif ($this->is_manager) {
    //         $this->selectedRole = 'manager';
    //     } elseif ($this->is_coordinator) {
    //         $this->selectedRole = 'coordinator';
    //     } elseif ($this->is_officer) {
    //         $this->selectedRole = 'officer';
    //     }
    // }


    protected function handleFileUploads(array $employeeData): array
    {
        // Handle NIDA file
        if ($this->nida_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->existing_nida_file) {
                Storage::disk('public')->delete($this->existing_nida_file);
            }
            $employeeData['nida_file'] = $this->nida_file->store('nida', 'public');
        } elseif (!$this->existing_nida_file && $this->isEditMode) {
            $employeeData['nida_file'] = null;
        }

        // Handle Zanzibar ID file
        if ($this->zan_id_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->existing_zan_id_file) {
                Storage::disk('public')->delete($this->existing_zan_id_file);
            }
            $employeeData['zan_id_file'] = $this->zan_id_file->store('zan_id', 'public');
        } elseif (!$this->existing_zan_id_file && $this->isEditMode) {
            $employeeData['zan_id_file'] = null;
        }

        // Handle Photo file
        if ($this->photo_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->existing_photo_file) {
                Storage::disk('public')->delete($this->existing_photo_file);
            }
            $employeeData['photo_file'] = $this->photo_file->store('photo', 'public');
        } elseif (!$this->existing_photo_file && $this->isEditMode) {
            $employeeData['photo_file'] = null;
        }
        // Handle Birth Certificate file
        if ($this->birth_certificate_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->existing_birth_certificate_file) {
                Storage::disk('public')->delete($this->existing_birth_certificate_file);
            }
            $employeeData['birth_certificate_file'] = $this->birth_certificate_file->store('birth_certificate', 'public');
        } elseif (!$this->existing_birth_certificate_file && $this->isEditMode) {
            $employeeData['birth_certificate_file'] = null;
        }
        // Handle Employee Contract file
        if ($this->employment_contract_file instanceof \Illuminate\Http\UploadedFile) {
            if ($this->existing_employment_contract_file) {
                Storage::disk('public')->delete($this->existing_employment_contract_file);
            }
            $employeeData['employment_contract_file'] = $this->employment_contract_file->store('employment_contracts', 'public');
        } elseif (!$this->existing_employment_contract_file && $this->isEditMode) {
            $employeeData['employment_contract_file'] = null;
        }

        return $employeeData;
    }

    protected function saveEducationLevels($employee)
    {
        // Delete existing education levels if updating
        if ($this->isEditMode) {
            $employee->education_levels()->delete();
        }

        foreach ($this->education_levels as $index => $edu) {
            $data = [
                'course_name' => $edu['course_name'],
                'certificate_name' => $edu['certificate_name'],
                'holder_certificate_no' => $edu['holder_certificate_no'] ?? null,
            ];

            // Handle new certificate file upload
            if (!empty($this->certificate_files[$index]) && $this->certificate_files[$index] instanceof \Illuminate\Http\UploadedFile) {
                $data['certificate_file'] = $this->certificate_files[$index]->store('certificates', 'public');
            } elseif (!empty($edu['existing_file'])) {
                // Keep existing file if no new file uploaded
                $data['certificate_file'] = $edu['existing_file'];
            }

            $employee->education_levels()->create($data);
        }
    }

    // ── SAVE METHODS (call from submitForm() inside the DB transaction) ───────

    protected function saveIdentifications(Employee $employee): void
    {
        if ($this->isEditMode) {
            $employee->identifications()->delete();
        }

        foreach ($this->identification_items as $index => $item) {
            $data = [
                'identification_id' => $item['identification_id'],
                'identification_no' => $item['identification_no'] ?? null,
            ];

            if (
                !empty($this->identification_upload_files[$index]) &&
                $this->identification_upload_files[$index] instanceof \Illuminate\Http\UploadedFile
            ) {
                $data['identification_path'] = $this->identification_upload_files[$index]
                    ->store('identifications', 'public');
            } elseif (!empty($item['existing_file'])) {
                $data['identification_path'] = $item['existing_file'];
            }

            $employee->identifications()->create($data);
        }
    }

    protected function saveCertificates(Employee $employee): void
    {
        if ($this->isEditMode) {
            $employee->certificates()->delete();
        }

        foreach ($this->certificate_items as $index => $item) {
            $data = [
                'certificate_id' => $item['certificate_id'],
                'certificate_no' => $item['certificate_no'] ?? null,
            ];

            if (
                !empty($this->certificate_upload_files[$index]) &&
                $this->certificate_upload_files[$index] instanceof \Illuminate\Http\UploadedFile
            ) {
                $data['certificate_path'] = $this->certificate_upload_files[$index]
                    ->store('certificates', 'public');
            } elseif (!empty($item['existing_file'])) {
                $data['certificate_path'] = $item['existing_file'];
            }

            $employee->certificates()->create($data);
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
            'email',
            'marital_status',
            'gender',
            'health_insurance_no',
            'designation',
            'opf_number',
            'social_security_no',
            'photo_file',
            'unit',
            'department',
            'selectedRole',
            'userId',
            'location',
            'disability',
            'is_officer',
            'identification_items',
            'identification_upload_files',
            'certificate_items',
            'certificate_upload_files',
            'contacts',
        ]);

        $this->currentStep = 1;
        $this->isEditMode = false;
        $this->initializeEducationLevels();
    }

    // ── ADD / REMOVE for identifications ─────────────────────────────────────

    public function addIdentificationItem(): void
    {
        $this->identification_items[] = [
            'identification_id' => '',
            'identification_no'  => '',
            'existing_file'      => null,
        ];
    }

    public function removeIdentificationItem(int $index): void
    {
        if (count($this->identification_items) > 1) {
            if (!empty($this->identification_items[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->identification_items[$index]['existing_file']);
            }
            unset($this->identification_items[$index]);
            $this->identification_items = array_values($this->identification_items);
        }
    }

    // ── ADD / REMOVE for certificates ────────────────────────────────────────

    public function addCertificateItem(): void
    {
        $this->certificate_items[] = [
            'certificate_id' => '',
            'certificate_no' => '',
            'existing_file'  => null,
        ];
    }

    public function removeCertificateItem(int $index): void
    {
        if (count($this->certificate_items) > 1) {
            if (!empty($this->certificate_items[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->certificate_items[$index]['existing_file']);
            }
            unset($this->certificate_items[$index]);
            $this->certificate_items = array_values($this->certificate_items);
        }
    }

    public function addEducationLevel()
    {
        $this->education_levels[] = [
            "course_name" => "",
            "certificate_name" => "",
            "holder_certificate_no" => "",
            "certificate_file" => "",
            "existing_file" => null
        ];
    }

    public function removeEducationLevel($index)
    {
        if (count($this->education_levels) > 1) {
            // Delete file if exists
            if (!empty($this->education_levels[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->education_levels[$index]['existing_file']);
            }

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
            'identificationTypes' => Identification::pluck('identification_name', 'id'),
            'certificateTypes'    => Certificate::pluck('certificate_name', 'id'),
            'isEditMode' => $this->isEditMode,
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
