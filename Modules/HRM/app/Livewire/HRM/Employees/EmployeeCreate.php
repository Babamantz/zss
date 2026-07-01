<?php

namespace Modules\HRM\Livewire\HRM\Employees;

use App\Models\Certificate;
use App\Models\Designation;
use App\Models\EducationLevel;
use App\Models\EmploymentType;
use App\Models\Identification;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\HRM\Enums\Gender;
use Modules\HRM\Enums\MaritalStatus;
use Modules\HRM\Models\Bank;
use Modules\HRM\Models\Department;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Unit;

class EmployeeCreate extends Component
{
    use WithFileUploads;

    // ── Identity & navigation ─────────────────────────────────────────────────
    public $userId;
    public $employeeId;
    public $mode;
    public $employee;
    public $employee_bank_id;


    public int $currentStep = 1;

    // ── Step 1: Basic Information ─────────────────────────────────────────────
    public $email;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $dob;
    public $hired_date;
    public $retiring_date;
    public $education;
    public $marital_status;
    public $gender;
    public bool $is_disable = false;
    public  $employment_type;

    // Contacts JSON (personal + next of kin)
    public array $contacts = [
        ['type' => 'personal',    'phone_number' => ''],
        ['type' => 'next_of_kin', 'phone_number' => ''],
    ];

    // ── Step 2: Employment Details ────────────────────────────────────────────
    public $opf_number;
    public $designation_id;          // FK → designations.id
    public $unit;
    public $employee_bank_account_no;
    public $department;
    public $location;
    // public bool $is_officer = true;

    // Files that stay on employees table
    public $photo_file;
    public $file_number;
    public $existing_photo_file;
    public $birth_certificate_file;
    public $existing_birth_certificate_file;
    public $employment_contract_file;
    public $existing_employment_contract_file;

    // ── Step 3: Identifications ───────────────────────────────────────────────
    public array $identification_items       = [];
    public array $identification_upload_files = [];

    // ── Step 4: Certificates (file-type docs) ─────────────────────────────────
    public array $certificate_items       = [];
    public array $certificate_upload_files = [];

    // ── Step 5: Education Levels ──────────────────────────────────────────────
    public array $education_levels  = [];
    public array $certificate_files = [];   // education-level cert uploads

    // ── Meta ──────────────────────────────────────────────────────────────────
    public bool $isEditMode = false;
    public bool $isViewMode = false;

    // ── Constants ─────────────────────────────────────────────────────────────
    

    

    protected $listeners = ['user-selected' => 'selectUser'];

    // =========================================================================
    // MOUNT
    // =========================================================================

    public function mount($employeeId = null, $mode = null): void
    {
        $this->employeeId = $employeeId;
        $this->isEditMode = !is_null($employeeId);
        $this->isViewMode = $mode === 'view';

        $this->initializeEducationLevels();
        $this->initializeIdentificationItems();
        $this->initializeCertificateItems();

        if ($employeeId) {
            $this->loadEmployee();
        }
    }

    // ── Initialisers ──────────────────────────────────────────────────────────

    protected function initializeEducationLevels(): void
    {
        if (empty($this->education_levels)) {
            $this->education_levels = [[
                'course_name'           => '',
                'certificate_name'      => '',
                'holder_certificate_no' => '',
                'certificate_file'      => '',
                'existing_file'         => null,
            ]];
        }
    }

    protected function initializeIdentificationItems(): void
    {
        if (empty($this->identification_items)) {
            $this->identification_items = [[
                'identification_id' => '',
                'identification_no' => '',
                'existing_file'     => null,
            ]];
        }
    }

    protected function initializeCertificateItems(): void
    {
        if (empty($this->certificate_items)) {
            $this->certificate_items = [[
                'certificate_id' => '',
                'certificate_no' => '',
                'existing_file'  => null,
            ]];
        }
    }

    // =========================================================================
    // LOAD EMPLOYEE (edit mode)
    // =========================================================================

    protected function loadEmployee(): void
    {
        $this->employee = Employee::with([
            'user',
            'user.tenant',
            'identifications',
            'certificates',
            'education_levels',
        ])->find($this->employeeId);

        if (!$this->employee) {
            session()->flash('error', 'Employee not found.');
            redirect()->route('hrm.employees.index');
            return;
        }

        $this->populateEmployeeData();
    }

    protected function populateEmployeeData(): void
    {
        $emp = $this->employee;
        // dd($emp->employment_type);

        // Scalars
        $this->fill([
            'dob'            => $emp->dob,
            'hired_date'     => $emp->hired_date,
            'retiring_date'  => $emp->retiring_date,
            'marital_status' => $emp->marital_status,
            'gender'         => $emp->gender,
            'opf_number'     => $emp->opf_number,
            'file_number'     => $emp->file_number,
            'education'      => $emp->education,
            'is_disable'     => (bool) $emp->is_disable,
            'employment_type'     => $emp->employment_type,
            // 'is_officer'     => (bool) $emp->is_officer,
            'designation_id' => $emp->designation_id,
            'unit'           => $emp->unit_id,
            'department'     => $emp->department_id,
        ]);

        // Existing files
        $this->existing_photo_file                = $emp->photo_file;
        $this->existing_birth_certificate_file    = $emp->birth_certificate_file;
        $this->existing_employment_contract_file  = $emp->employment_contract_file;

        // User / email
        $this->userId     = $emp->user_id;
        $this->email      = $emp->user_id;
        $this->first_name = $emp->user->first_name;
        $this->middle_name = $emp->user->middle_name;
        $this->last_name  = $emp->user->last_name;
        $this->location   = $emp->user->tenant?->name;

        // Contacts JSON
        $saved = json_decode($emp->contacts, true);
        $this->contacts = !empty($saved) ? $saved : $this->contacts;

        // Identifications
        $loaded = $emp->identifications->map(fn($i) => [
            'identification_id' => $i->identification_id,
            'identification_no' => $i->identification_no,
            'existing_file'     => $i->identification_path,
        ])->toArray();
        $this->identification_items = !empty($loaded) ? $loaded : $this->identification_items;

        // Certificates (file-type docs)
        $loaded = $emp->certificates->map(fn($c) => [
            'certificate_id' => $c->certificate_id,
            'certificate_no' => $c->certificate_no,
            'existing_file'  => $c->certificate_path,
        ])->toArray();
        $this->certificate_items = !empty($loaded) ? $loaded : $this->certificate_items;

        // Education levels
        $loaded = $emp->education_levels->map(fn($edu) => [
            'id'                    => $edu->id,
            'course_name'           => $edu->course_name,
            'certificate_name'      => $edu->certificate_name,
            'holder_certificate_no' => $edu->holder_certificate_no,
            'certificate_file'      => $edu->certificate_file,
            'existing_file'         => $edu->certificate_file,
        ])->toArray();
        $this->education_levels = !empty($loaded) ? $loaded : $this->education_levels;

        $this->loadEmail();
    }

    // =========================================================================
    // USER SELECTION
    // =========================================================================

    #[On('user-selected')]
    public function selectUser(): void
    {
        if (!$this->email) return;

        try {
            $user = User::with('tenant')->findOrFail($this->email);
            $this->first_name  = $user->first_name;
            $this->middle_name = $user->middle_name;
            $this->last_name   = $user->last_name;
            $this->userId      = $user->id;
            $this->location    = $user->tenant?->name;
        } catch (Exception $e) {
            session()->flash('error', 'User not found.');
            Log::error('User Selection Error: ' . $e->getMessage());
        }
    }

    public function loadEmail(): void
    {
        if ($this->employee?->user) {
            $this->dispatch('eventEmail', [
                'employee_id'   => $this->employee->user->id,
                'unit_id'       => $this->employee->unit_id,
                'department_id' => $this->employee->department_id,
            ]);
        }
    }

    // =========================================================================
    // VALIDATION
    // =========================================================================

    public function rules(): array
    {
        return [
            // Step 1
            'first_name'              => 'required|string|min:2|max:50',
            'middle_name'             => 'nullable|string|min:2|max:50',
            'last_name'               => 'required|string|min:2|max:50',
            'dob'                     => 'required|date|before:today',
            'hired_date'              => 'required|date',
            'retiring_date'           => 'nullable|date|after:hired_date',
            'education'               => 'required|string',
            'marital_status'          => 'required|string|in:' . implode(',', MaritalStatus::ALL),
            'is_disable'              => 'required|boolean',
            'employment_type' => 'required|integer',
            'gender'          => 'required|string|in:' . implode(',', Gender::ALL),
            'email'                   => 'required|exists:users,id',
            'contacts.0.phone_number' => 'required|string|max:15',
            'contacts.1.phone_number' => 'nullable|string|max:15',

            // Step 2
            'opf_number'     => 'nullable|string|max:50',
            'designation_id' => 'required|exists:designations,id',
            'unit'           => 'nullable|exists:units,id',
            'department'     => 'nullable|exists:departments,id',
            'file_number'     => 'nullable|string|max:50',
            'photo_file'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'birth_certificate_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'employment_contract_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Step 3
            'identification_items.*.identification_id' => 'required|exists:identifications,id',
            'identification_items.*.identification_no' => 'nullable|string|max:60',

            // Step 4
            'certificate_items.*.certificate_id' => 'required|exists:certificates,id',
            'certificate_items.*.certificate_no' => 'nullable|string|max:60',

            // Step 5
            'education_levels.*.course_name'           => 'required|string|max:100',
            'education_levels.*.certificate_name'      => 'required|string|max:100',
            'education_levels.*.holder_certificate_no' => 'nullable|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required'                           => 'Please select a user email.',
            'email.exists'                             => 'The selected user does not exist.',
            'dob.before'                               => 'Date of birth must be before today.',
            'designation_id.required'                  => 'Please select a designation.',
            'designation_id.exists'                    => 'Selected designation is invalid.',
            'education.in'                             => 'Please select a valid education level.',
            'marital_status.in'                        => 'Please select a valid marital status.',
            'retiring_date.after'                      => 'Retiring date must be after hired date.',
            'contacts.0.phone_number.required'         => 'Personal phone number is required.',
            'identification_items.*.identification_id.required' => 'Please select an identification type.',
            'certificate_items.*.certificate_id.required'       => 'Please select a certificate type.',
            'education_levels.*.course_name.required'           => 'Course name is required.',
            'education_levels.*.certificate_name.required'      => 'Certificate name is required.',
        ];
    }

    protected function getStepRules(int $step): array
    {
        $all = $this->rules();

        $stepFields = [
            1 => [
                'first_name',
                'middle_name',
                'last_name',
                'dob',
                'hired_date',
                'retiring_date',
                'education',
                'marital_status',
                'gender',
                'is_disable',
                'employment_type',
                'email',
                'contacts.0.phone_number',
                'contacts.1.phone_number',
            ],
            2 => [
                'opf_number',
                'designation_id',
                'unit',
                'department',
                'file_number',
                'photo_file',
                'birth_certificate_file',
                'employment_contract_file',
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

        return array_intersect_key($all, array_flip($stepFields[$step] ?? []));
    }

    // =========================================================================
    // STEP NAVIGATION
    // =========================================================================

    public function nextStep(): void
    {
        $this->validate($this->getStepRules($this->currentStep));
        $this->currentStep++;
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // =========================================================================
    // FORM SUBMISSION
    // =========================================================================

    public function submitForm(): void
    {
        // dd($this->employment_type); // in your save() method, before validation
        $this->validate();
        DB::beginTransaction();

        try {
            $employee = $this->saveEmployee();
            $this->saveEmployeeBankDetails($employee);
            $this->saveEducationLevels($employee);
            $this->saveIdentifications($employee);
            $this->saveCertificates($employee);

            DB::commit();

            session()->flash(
                'success',
                $this->isEditMode
                    ? 'Employee updated successfully!'
                    : 'Employee created successfully!'
            );

            if (!$this->isEditMode) {
                $this->resetFormFields();
                $this->dispatch('resetFileState');
            }

            redirect()->route('hrm.employees.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee Form Submission Error', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            session()->flash('error', 'Failed to save employee. Please try again.');
        }
    }

    // =========================================================================
    // SAVE HELPERS
    // =========================================================================

    protected function saveEmployee(): Employee
    {
        $data = [
            'dob'            => $this->dob,
            'hired_date'     => $this->hired_date,
            'retiring_date'  => $this->retiring_date,
            'marital_status' => $this->marital_status,
            'gender'         => $this->gender,
            'opf_number'     => $this->opf_number,
            'education'      => $this->education,
            'is_disable'     => $this->is_disable,
            'employment_type'     => $this->employment_type,
            'is_active'      => 'active',
            'is_hr_registered' => true,
            // 'is_officer'     => $this->is_officer,
            'designation_id' => $this->designation_id,
            'unit_id'        => $this->unit,
            'department_id'  => $this->department,
            'user_id'        => $this->userId,
            'contacts'       => json_encode(
                array_values(array_filter(
                    $this->contacts,
                    fn($c) => filled($c['phone_number'])
                ))
            ),
        ];

        $data = $this->handleFileUploads($data);

        if ($this->isEditMode) {
            $data['updated_by'] = auth()->id();
            $employee = Employee::findOrFail($this->employeeId);
            $employee->update($data);
        } else {
            $data['created_by'] = auth()->id();
            $employee = Employee::create($data);
        }

        return $employee;
    }

    protected function saveEmployeeBankDetails(Employee $employee)
    {
        // dd($this->employee_bank_account_no);
        $employee->bankAccount()->create([
            'account_no' => $this->employee_bank_account_no,
            'bank_id' => $this->employee_bank_id,
        ]);
    }

    protected function handleFileUploads(array $data): array
    {
        $uploads = [
            'photo_file'               => ['field' => 'photo_file',              'existing' => 'existing_photo_file',               'dir' => 'photos'],
            'birth_certificate_file'   => ['field' => 'birth_certificate_file',  'existing' => 'existing_birth_certificate_file',    'dir' => 'birth_certificates'],
            'employment_contract_file' => ['field' => 'employment_contract_file', 'existing' => 'existing_employment_contract_file',  'dir' => 'employment_contracts'],
        ];

        foreach ($uploads as $column => $cfg) {
            $file     = $this->{$cfg['field']};
            $existing = $this->{$cfg['existing']};

            if ($file instanceof \Illuminate\Http\UploadedFile) {
                if ($existing) Storage::disk('public')->delete($existing);
                $data[$column] = $file->store($cfg['dir'], 'public');
            } elseif ($this->isEditMode) {
                // Preserve existing path; null if it was explicitly cleared
                $data[$column] = $existing ?: null;
            }
        }

        return $data;
    }

    protected function saveEducationLevels(Employee $employee): void
    {
        if ($this->isEditMode) {
            $employee->education_levels()->delete();
        }

        foreach ($this->education_levels as $index => $edu) {
            $row = [
                'course_name'           => $edu['course_name'],
                'certificate_name'      => $edu['certificate_name'],
                'holder_certificate_no' => $edu['holder_certificate_no'] ?? null,
            ];

            if (
                !empty($this->certificate_files[$index])
                && $this->certificate_files[$index] instanceof \Illuminate\Http\UploadedFile
            ) {
                $row['certificate_file'] = $this->certificate_files[$index]->store('edu_certificates', 'public');
            } elseif (!empty($edu['existing_file'])) {
                $row['certificate_file'] = $edu['existing_file'];
            }

            $employee->education_levels()->create($row);
        }
    }

    protected function saveIdentifications(Employee $employee): void
    {
        if ($this->isEditMode) {
            $employee->identifications()->delete();
        }

        foreach ($this->identification_items as $index => $item) {
            $row = [
                'identification_id' => $item['identification_id'],
                'identification_no' => $item['identification_no'] ?? null,
            ];

            if (
                !empty($this->identification_upload_files[$index])
                && $this->identification_upload_files[$index] instanceof \Illuminate\Http\UploadedFile
            ) {
                $row['identification_path'] = $this->identification_upload_files[$index]
                    ->store('identifications', 'public');
            } elseif (!empty($item['existing_file'])) {
                $row['identification_path'] = $item['existing_file'];
            }

            $employee->identifications()->create($row);
        }
    }

    protected function saveCertificates(Employee $employee): void
    {
        if ($this->isEditMode) {
            $employee->certificates()->delete();
        }

        foreach ($this->certificate_items as $index => $item) {
            $row = [
                'certificate_id' => $item['certificate_id'],
                'certificate_no' => $item['certificate_no'] ?? null,
            ];

            if (
                !empty($this->certificate_upload_files[$index])
                && $this->certificate_upload_files[$index] instanceof \Illuminate\Http\UploadedFile
            ) {
                $row['certificate_path'] = $this->certificate_upload_files[$index]
                    ->store('cert_files', 'public');
            } elseif (!empty($item['existing_file'])) {
                $row['certificate_path'] = $item['existing_file'];
            }

            $employee->certificates()->create($row);
        }
    }

    // =========================================================================
    // ADD / REMOVE ROWS
    // =========================================================================

    public function addEducationLevel(): void
    {
        $this->education_levels[] = [
            'course_name' => '',
            'certificate_name' => '',
            'holder_certificate_no' => '',
            'certificate_file' => '',
            'existing_file' => null,
        ];
    }

    public function removeEducationLevel(int $index): void
    {
        if (count($this->education_levels) > 1) {
            if (!empty($this->education_levels[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->education_levels[$index]['existing_file']);
            }
            array_splice($this->education_levels, $index, 1);
        }
    }

    public function addIdentificationItem(): void
    {
        $this->identification_items[] = [
            'identification_id' => '',
            'identification_no' => '',
            'existing_file' => null,
        ];
    }

    public function removeIdentificationItem(int $index): void
    {
        if (count($this->identification_items) > 1) {
            if (!empty($this->identification_items[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->identification_items[$index]['existing_file']);
            }
            array_splice($this->identification_items, $index, 1);
        }
    }

    public function addCertificateItem(): void
    {
        $this->certificate_items[] = [
            'certificate_id' => '',
            'certificate_no' => '',
            'existing_file' => null,
        ];
    }

    public function removeCertificateItem(int $index): void
    {
        if (count($this->certificate_items) > 1) {
            if (!empty($this->certificate_items[$index]['existing_file'])) {
                Storage::disk('public')->delete($this->certificate_items[$index]['existing_file']);
            }
            array_splice($this->certificate_items, $index, 1);
        }
    }

    // =========================================================================
    // RESET
    // =========================================================================

    public function resetFormFields(): void
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'dob',
            'education',
            'hired_date',
            'retiring_date',
            'email',
            'marital_status',
            'gender',
            'is_disable',
            'employment_type',
            'opf_number',
            'designation_id',
            'unit',
            'department',
            'file_number',
            'photo_file',
            'existing_photo_file',
            'birth_certificate_file',
            'existing_birth_certificate_file',
            'employment_contract_file',
            'existing_employment_contract_file',
            'userId',
            'location',
            'identification_items',
            'identification_upload_files',
            'certificate_items',
            'certificate_upload_files',
            'education_levels',
            'certificate_files',
            'employee_bank_account_no',
            'employee_bank_id'
        ]);

        $this->contacts = [
            ['type' => 'personal',    'phone_number' => ''],
            ['type' => 'next_of_kin', 'phone_number' => ''],
        ];

        $this->currentStep = 1;
        $this->isEditMode  = false;

        $this->initializeEducationLevels();
        $this->initializeIdentificationItems();
        $this->initializeCertificateItems();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('hrm::livewire.h-r-m.employees.employee-create', [
            'departments'         => Department::pluck('name', 'id'),
            'units'               => Unit::pluck('name', 'id'),
            'banks'               => Bank::pluck('name', 'id'),
            'emails'              => $this->getAvailableEmails(),
            'educationLevels'     => EducationLevel::pluck('name','id'),
            'employmentTypes'     => EmploymentType::pluck('name','id'),
            'designations'        => Designation::pluck('designation_name', 'id'),
            'identificationTypes' => Identification::pluck('identification_name', 'id'),
            'certificateTypes'    => Certificate::pluck('certificate_name', 'id'),
            'isEditMode'          => $this->isEditMode,
        ]);
    }

    protected function getAvailableEmails(): \Illuminate\Support\Collection
    {
        return User::query()
            ->when(!$this->employeeId, fn($q) => $q->doesntHave('employee'))
            ->orderByDesc('created_at')
            ->pluck('email', 'id');
    }
}
