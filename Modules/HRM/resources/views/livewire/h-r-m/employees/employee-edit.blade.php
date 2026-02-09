@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
    <style>
        .file-preview-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .file-preview-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 0.375rem;
            border: 2px solid #dee2e6;
        }

        .file-preview-icon {
            width: 80px;
            height: 80px;
            background-color: #e9ecef;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $employeeId ? 'Edit Employee' : 'Create Employee' }}</h5>
                    <a class="btn btn-primary" href="{{ route('hrm.employee.index') }}">Back</a>
                </div>
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Step Indicator --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <div class="text-center {{ $currentStep >= 1 ? 'text-primary' : 'text-muted' }}">
                                <div class="mb-2">
                                    <span
                                        class="badge {{ $currentStep >= 1 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">1</span>
                                </div>
                                <small>Basic Info</small>
                            </div>
                            <div class="text-center {{ $currentStep >= 2 ? 'text-primary' : 'text-muted' }}">
                                <div class="mb-2">
                                    <span
                                        class="badge {{ $currentStep >= 2 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">2</span>
                                </div>
                                <small>Employment Details</small>
                            </div>
                            <div class="text-center {{ $currentStep >= 3 ? 'text-primary' : 'text-muted' }}">
                                <div class="mb-2">
                                    <span
                                        class="badge {{ $currentStep >= 3 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">3</span>
                                </div>
                                <small>Education</small>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="submitForm">
                        <input type="hidden" wire:model="userId">

                        {{-- Step 1: Basic Information --}}
                        <div @if ($currentStep != 1) style="display: none;" @endif>
                            <h5 class="mb-3">Basic Information</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="select-user">Email: <span class="text-danger">*</span></label>
                                        <select wire:model="email" id="select-user"
                                            class="js-example-basic-single form-control"
                                            {{ $isEditMode ? 'disabled' : '' }}>
                                            <option value="">--Select Email--</option>
                                            @foreach ($emails as $id => $emailText)
                                                <option value="{{ $id }}">{{ $emailText }}</option>
                                            @endforeach
                                        </select>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName">First Name: <span class="text-danger">*</span></label>
                                        <input class="form-control" id="firstName" type="text"
                                            wire:model.defer="first_name" readonly>
                                        @error('first_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="middleName">Middle Name:</label>
                                        <input class="form-control" id="middleName" type="text"
                                            wire:model.defer="middle_name" readonly>
                                        @error('middle_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName">Last Name: <span class="text-danger">*</span></label>
                                        <input class="form-control" id="lastName" type="text"
                                            wire:model.defer="last_name" readonly>
                                        @error('last_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dob">Date of Birth: <span class="text-danger">*</span></label>
                                        <input class="form-control" id="dob" type="date"
                                            wire:model.defer="dob">
                                        @error('dob')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hired_date">Hired Date: <span class="text-danger">*</span></label>
                                        <input class="form-control" id="hired_date" type="date"
                                            wire:model.defer="hired_date">
                                        @error('hired_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="educationLevel">Education Level: <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control js-example-basic-single" id="educationLevel"
                                            wire:model.defer="education">
                                            <option value="">--Select Education Level--</option>
                                            @foreach ($educationLevels as $level)
                                                <option value="{{ $level }}">{{ ucfirst($level) }}</option>
                                            @endforeach
                                        </select>
                                        @error('education')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="retiring_date">Retiring Date:</label>
                                        <input class="form-control" id="retiring_date" type="date"
                                            wire:model.defer="retiring_date">
                                        @error('retiring_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="select-bank">Bank Name: <span class="text-danger">*</span></label>
                                        <select wire:model.defer="bank_name" id="select-bank"
                                            class="js-example-basic-single form-control">
                                            <option value="">--Select Bank--</option>
                                            @foreach ($banks as $id => $bank)
                                                <option value="{{ $id }}">{{ $bank }}</option>
                                            @endforeach
                                        </select>
                                        @error('bank_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bankAccountNumber">Bank Account Number: <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" id="bankAccountNumber" type="text"
                                            wire:model.defer="bank_account_no">
                                        @error('bank_account_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phoneNumber">Phone Number: <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" id="phoneNumber" type="tel"
                                            wire:model.defer="phone_number">
                                        @error('phone_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Disability: <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="disability_yes"
                                                    wire:model.defer="disability" value="yes">
                                                <label class="form-check-label" for="disability_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="disability_no"
                                                    wire:model.defer="disability" value="no">
                                                <label class="form-check-label" for="disability_no">No</label>
                                            </div>
                                        </div>
                                        @error('disability')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Marital Status: <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="single"
                                                    wire:model.defer="marital_status" value="single">
                                                <label class="form-check-label" for="single">Single</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="married"
                                                    wire:model.defer="marital_status" value="married">
                                                <label class="form-check-label" for="married">Married</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="divorced"
                                                    wire:model.defer="marital_status" value="divorced">
                                                <label class="form-check-label" for="divorced">Divorced</label>
                                            </div>
                                        </div>
                                        @error('marital_status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender: <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="male"
                                                    wire:model.defer="gender" value="male">
                                                <label class="form-check-label" for="male">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="female"
                                                    wire:model.defer="gender" value="female">
                                                <label class="form-check-label" for="female">Female</label>
                                            </div>
                                        </div>
                                        @error('gender')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- Step 2: Employment Details --}}
                        <div @if ($currentStep != 2) style="display: none;" @endif>
                            <h5 class="mb-3">Employment Details</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="healthInsuranceNumber">Health Insurance Number:</label>
                                        <input class="form-control" id="healthInsuranceNumber" type="text"
                                            wire:model.defer="health_insurance_no">
                                        @error('health_insurance_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="designation">Designation:</label>
                                        <input class="form-control" id="designation" type="text"
                                            wire:model.defer="designation">
                                        @error('designation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payrollNumber">Payroll Number:</label>
                                        <input class="form-control" id="payrollNumber" type="text"
                                            wire:model.defer="opf_number">
                                        @error('opf_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="socialSecurityNumber">Social Security Number:</label>
                                        <input class="form-control" id="socialSecurityNumber" type="text"
                                            wire:model.defer="social_security_no">
                                        @error('social_security_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zanId">Zanzibar ID Number:</label>
                                        <input class="form-control" id="zanId" type="text"
                                            wire:model.defer="zan_id_no">
                                        @error('zan_id_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zan_id_file">Zanzibar ID File:</label>

                                        {{-- Preview existing Zanzibar ID file --}}
                                        @if ($isEditMode && $existing_zan_id_file)
                                            <div class="file-preview-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        @if ($this->isImageFile($existing_zan_id_file))
                                                            <img src="{{ $this->getFileUrl($existing_zan_id_file) }}"
                                                                alt="Zanzibar ID" class="file-preview-image me-3">
                                                        @else
                                                            <div class="file-preview-icon me-3">
                                                                <i class="fa fa-file-pdf fa-3x text-danger"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="mb-0 fw-bold">Current File</p>
                                                            <small
                                                                class="text-muted">{{ basename($existing_zan_id_file) }}</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" wire:click="downloadFile('zan_id')"
                                                            class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="fa fa-download"></i> Download
                                                        </button>
                                                        <button type="button"
                                                            wire:click="removeExistingFile('zan_id')"
                                                            onclick="return confirm('Remove this file?')"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <input class="form-control" type="file" id="zan_id_file"
                                            wire:model="zan_id_file" accept=".jpg,.jpeg,.png,.pdf">
                                        @error('zan_id_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="zan_id_file" class="text-primary mt-1">
                                            <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nidaNo">NIDA Number:</label>
                                        <input class="form-control" id="nidaNo" type="text"
                                            wire:model.defer="nida_no">
                                        @error('nida_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nida_file">NIDA File:</label>

                                        {{-- Preview existing NIDA file --}}
                                        @if ($isEditMode && $existing_nida_file)
                                            <div class="file-preview-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        @if ($this->isImageFile($existing_nida_file))
                                                            <img src="{{ $this->getFileUrl($existing_nida_file) }}"
                                                                alt="NIDA" class="file-preview-image me-3">
                                                        @else
                                                            <div class="file-preview-icon me-3">
                                                                <i class="fa fa-file-pdf fa-3x text-danger"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="mb-0 fw-bold">Current File</p>
                                                            <small
                                                                class="text-muted">{{ basename($existing_nida_file) }}</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" wire:click="downloadFile('nida')"
                                                            class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="fa fa-download"></i> Download
                                                        </button>
                                                        <button type="button" wire:click="removeExistingFile('nida')"
                                                            onclick="return confirm('Remove this file?')"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <input class="form-control" type="file" id="nida_file"
                                            wire:model="nida_file" accept=".jpg,.jpeg,.png,.pdf">
                                        @error('nida_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="nida_file" class="text-primary mt-1">
                                            <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="photo_file">Employee Photo:</label>

                                        {{-- Preview existing photo --}}
                                        @if ($isEditMode && $existing_photo_file)
                                            <div class="file-preview-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        @if ($this->isImageFile($existing_photo_file))
                                                            <img src="{{ $this->getFileUrl($existing_photo_file) }}"
                                                                alt="Photo" class="photo-preview me-3">
                                                        @else
                                                            <div class="file-preview-icon me-3">
                                                                <i class="fa fa-user fa-3x text-secondary"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="mb-0 fw-bold">Current Photo</p>
                                                            <small
                                                                class="text-muted">{{ basename($existing_photo_file) }}</small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" wire:click="downloadFile('photo')"
                                                            class="btn btn-sm btn-outline-primary me-2">
                                                            <i class="fa fa-download"></i> Download
                                                        </button>
                                                        <button type="button"
                                                            wire:click="removeExistingFile('photo')"
                                                            onclick="return confirm('Remove this photo?')"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <input class="form-control" type="file" id="photo_file"
                                            wire:model="photo_file" accept=".jpg,.jpeg,.png">
                                        @error('photo_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="photo_file" class="text-primary mt-1">
                                            <small><i class="fa fa-spinner fa-spin"></i> Uploading...</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="select-role">Role: <span class="text-danger">*</span></label>
                                        <select wire:model="selectedRole" id="select-role"
                                            class="js-example-basic-single form-control">
                                            <option value="">--Select Role--</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}">
                                                    {{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedRole')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location:</label>
                                        <input class="form-control" id="location" type="text"
                                            wire:model.defer="location" readonly>
                                        @error('location')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="select-unit">Unit:</label>
                                        <select wire:model.defer="unit" id="select-unit"
                                            class="js-example-basic-single form-control">
                                            <option value="">--Select Unit--</option>
                                            @foreach ($units as $id => $unitName)
                                                <option value="{{ $id }}">{{ $unitName }}</option>
                                            @endforeach
                                        </select>
                                        @error('unit')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label for="select-department">Department:</label>
                                        <select wire:model="department" id="select-department"
                                            class="js-example-basic-single form-control">
                                            <option value="">--Select Department--</option>
                                            @foreach ($departments as $id => $departmentName)
                                                <option value="{{ $id }}">{{ $departmentName }}</option>
                                            @endforeach
                                        </select>
                                        @error('department')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary"
                                    wire:click="previousStep">Back</button>
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- Step 3: Education Levels --}}
                        <div @if ($currentStep != 3) style="display: none;" @endif>
                            <div x-data="{ educationLevels: @entangle('education_levels') }">
                                <div class="pb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Education Details</h5>
                                    <button type="button" class="btn btn-success btn-sm"
                                        wire:click="addEducationLevel">
                                        <i class="fa fa-plus"></i> Add Education
                                    </button>
                                </div>

                                <template x-for="(edu, index) in educationLevels" :key="index">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Certificate File:</label>

                                                        {{-- Preview existing certificate file --}}
                                                        <template x-if="$isEditMode && edu.existing_file">
                                                            <div class="file-preview-card mb-2">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="file-preview-icon me-2">
                                                                            <i
                                                                                class="fa fa-file-alt fa-2x text-info"></i>
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0 small fw-bold">Current
                                                                                Certificate</p>
                                                                            <small class="text-muted"
                                                                                x-text="edu.existing_file ? edu.existing_file.split('/').pop() : ''"></small>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button"
                                                                            @click="$wire.downloadFile('certificate', index)"
                                                                            class="btn btn-sm btn-outline-primary me-1">
                                                                            <i class="fa fa-download"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                            @click="if(confirm('Remove this certificate?')) $wire.removeExistingFile('certificate', index)"
                                                                            class="btn btn-sm btn-outline-danger">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <input type="file" class="form-control"
                                                            accept=".jpg,.jpeg,.png,.pdf"
                                                            @change="$wire.upload(`certificate_files.${index}`, $event.target.files[0])">
                                                        <div wire:loading wire:target="certificate_files"
                                                            class="text-primary mt-1">
                                                            <small><i class="fa fa-spinner fa-spin"></i>
                                                                Uploading...</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end" x-show="educationLevels.length > 1">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="if(confirm('Remove this education record?')) $wire.removeEducationLevel(index)">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-secondary"
                                        wire:click="previousStep">Back</button>
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submitForm">
                                            <i class="fa fa-save"></i>
                                            {{ $isEditMode ? 'Update Employee' : 'Create Employee' }}
                                        </span>
                                        <span wire:loading wire:target="submitForm">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            {{ $isEditMode ? 'Updating...' : 'Saving...' }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('./assets/js/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('./assets/js/select2/select2-custom.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize Select2 on email dropdown
            $('#select-user').select2();
            $('#select-user').on('change', function(e) {
                @this.set('email', e.target.value);
                @this.dispatch('user-selected');
            });

            // Initialize Select2 on education level dropdown
            $('#educationLevel').select2();
            $('#educationLevel').on('change', function(e) {
                @this.set('education', e.target.value);
            });

            // Initialize Select2 on bank dropdown
            $('#select-bank').select2();
            $('#select-bank').on('change', function(e) {
                @this.set('bank_name', e.target.value);
            });

            // Initialize Select2 on role dropdown
            $('#select-role').select2();
            $('#select-role').on('change', function(e) {
                @this.set('selectedRole', e.target.value);
            });

            // Initialize Select2 on unit dropdown
            $('#select-unit').select2();
            $('#select-unit').on('change', function(e) {
                @this.set('unit', e.target.value);
            });

            // Initialize Select2 on department dropdown
            $('#select-department').select2();
            $('#select-department').on('change', function(e) {
                @this.set('department', e.target.value);
            });

            // Listen for eventEmail to update Select2
            Livewire.on('eventEmail', (data) => {
                console.log('Event email received:', data);
                $('#select-user').val(data[0].employee_id).trigger('change');
                $('#select-role').val(data[0].employee_role).trigger('change');
                $('#select-bank').val(data[0].bank_name).trigger('change');
                $('#select-unit').val(data[0].unit_id).trigger('change');
                $('#select-department').val(data[0].department_id).trigger('change');
                $('#educationLevel').val(data[0].education).trigger('change');
            });

            // Reset file inputs when needed
            Livewire.on('resetFileState', () => {
                document.getElementById('zan_id_file').value = '';
                document.getElementById('nida_file').value = '';
                document.getElementById('photo_file').value = '';
            });
        });
    </script>
@endpush
