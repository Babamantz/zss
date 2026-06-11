@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{ $employeeId ? 'Edit Employee' : 'Create Employee' }}</h5>
                    <a class="btn btn-primary" href="{{ route('hrm.employees.index') }}">Back</a>
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
                                <small>IDs</small>
                            </div>
                            <div class="text-center {{ $currentStep >= 4 ? 'text-primary' : 'text-muted' }}">
                                <div class="mb-2">
                                    <span
                                        class="badge {{ $currentStep >= 4 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">4</span>
                                </div>
                                <small>File Type/Certificates</small>
                            </div>
                            <div class="text-center {{ $currentStep >= 5 ? 'text-primary' : 'text-muted' }}">
                                <div class="mb-2">
                                    <span
                                        class="badge {{ $currentStep >= 5 ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">5</span>
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
                                            class="js-example-basic-single form-control">
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
                                            wire:model.defer="first_name">
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
                                            wire:model.defer="middle_name">
                                        @error('middle_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName">Last Name: <span class="text-danger">*</span></label>
                                        <input class="form-control" id="lastName" type="text"
                                            wire:model.defer="last_name">
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
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
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
                                        <label>Personal Phone: <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control"
                                            wire:model.defer="contacts.0.phone_number" placeholder="+255 7XX XXX XXX">
                                        @error('contacts.0.phone_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Next of Keen: <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control"
                                            wire:model.defer="contacts.1.phone_number" placeholder="+255 7XX XXX XXX">
                                        @error('contacts.1.phone_number')
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
                                        <label for="payrollNumber">Payroll Number: <span
                                                class="text-danger">*</span></label>
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
                                    <div wire:ignore class="form-group">
                                        <label for="select-role">Designation: <span
                                                class="text-danger">*</span></label>
                                        <select wire:model="selectedRole" id="selected-role"
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

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="photo_file">PHOTO:</label>
                                        <input class="form-control" type="file" id="photo_file"
                                            wire:model="photo_file">
                                        @error('photo_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="photo_file" class="text-primary mt-1">
                                            <small>Uploading...</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Image Preview --}}
                                @if ($existing_photo_file)
                                    <div class="mt-4">
                                        Photo Preview:
                                        <img src="{{ Storage::disk('public')->url($existing_photo_file) }}"
                                            class="mt-2 rounded"
                                            style="width: 60px; height: 60px; object-fit: cover;">

                                    </div>
                                @endif

                                @error('photo')
                                    <span class="error">{{ $message }}</span>
                                @enderror


                            </div>

                            <div class="row mb-3">
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

                        {{-- Step 4: Identifications --}}
                        <div @if ($currentStep != 3) style="display:none;" @endif>
                            <div x-data="{ identificationItems: @entangle('identification_items') }">

                                <div class="pb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Identification Documents</h5>
                                    <button type="button" class="btn btn-success btn-sm"
                                        wire:click="addIdentificationItem">
                                        <i class="fa fa-plus"></i> Add Identification
                                    </button>
                                </div>

                                <template x-for="(item, index) in identificationItems" :key="index">
                                    <div class="card mb-3">
                                        <div class="card-body">

                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Identification Type: <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" x-model="item.identification_id">
                                                            <option value="">-- Select Type --</option>
                                                            @foreach ($identificationTypes as $id => $name)
                                                                <option value="{{ $id }}">
                                                                    {{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Identification Number:</label>
                                                        <input type="text" class="form-control"
                                                            x-model="item.identification_no">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-6" x-data="{ getPdfUrl() { return item.existing_file ? '{{ Storage::disk('public')->url('') }}' + item.existing_file : null } }">
                                                    <div class="form-group">
                                                        <label>Identification File:</label>
                                                        <input type="file" class="form-control"
                                                            @change="$wire.upload(`identification_upload_files.${index}`, $event.target.files[0])">

                                                        <template x-if="item.existing_file">
                                                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                                                @click="window.open(getPdfUrl(), '_blank')">
                                                                Preview PDF
                                                            </button>
                                                        </template>

                                                        <div wire:loading wire:target="identification_upload_files"
                                                            class="text-primary mt-1">
                                                            <small>Uploading...</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end" x-show="identificationItems.length > 1">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeIdentificationItem(index)">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </template>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-secondary"
                                        wire:click="previousStep">Back</button>
                                    <button type="button" class="btn btn-primary"
                                        wire:click="nextStep">Next</button>
                                </div>

                            </div>
                        </div>

                        {{-- Step 5: Certificates --}}
                        <div @if ($currentStep != 4) style="display:none;" @endif>
                            <div x-data="{ certificateItems: @entangle('certificate_items') }">

                                <div class="pb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Certificate Documents</h5>
                                    <button type="button" class="btn btn-success btn-sm"
                                        wire:click="addCertificateItem">
                                        <i class="fa fa-plus"></i> Add Certificate
                                    </button>
                                </div>

                                <template x-for="(item, index) in certificateItems" :key="index">
                                    <div class="card mb-3">
                                        <div class="card-body">

                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Certificate Type: <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" x-model="item.certificate_id">
                                                            <option value="">-- Select Type --</option>
                                                            @foreach ($certificateTypes as $id => $name)
                                                                <option value="{{ $id }}">
                                                                    {{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Certificate Number:</label>
                                                        <input type="text" class="form-control"
                                                            x-model="item.certificate_no">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-6" x-data="{ getPdfUrl() { return item.existing_file ? '{{ Storage::disk('public')->url('') }}' + item.existing_file : null } }">
                                                    <div class="form-group">
                                                        <label>Certificate File:</label>
                                                        <input type="file" class="form-control"
                                                            @change="$wire.upload(`certificate_upload_files.${index}`, $event.target.files[0])">

                                                        <template x-if="item.existing_file">
                                                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                                                @click="window.open(getPdfUrl(), '_blank')">
                                                                Preview PDF
                                                            </button>
                                                        </template>

                                                        <div wire:loading wire:target="certificate_upload_files"
                                                            class="text-primary mt-1">
                                                            <small>Uploading...</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end" x-show="certificateItems.length > 1">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeCertificateItem(index)">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </template>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-secondary"
                                        wire:click="previousStep">Back</button>
                                    <button type="button" class="btn btn-primary"
                                        wire:click="nextStep">Next</button>
                                </div>

                            </div>
                        </div>

                        {{-- Step 3: Education Levels --}}
                        <div @if ($currentStep != 5) style="display: none;" @endif>
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
                                                        <label>Course Name: <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            x-model="edu.course_name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Certificate Name: <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            x-model="edu.certificate_name">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Certificate Number:</label>
                                                        <input type="text" class="form-control"
                                                            x-model="edu.holder_certificate_no">
                                                    </div>
                                                </div>
                                                <div class="col-md-6" x-data="{ getPdfUrl() { return edu.existing_file ? '{{ Storage::disk('public')->url('') }}/' + edu.existing_file : null } }">
                                                    <div class="form-group">
                                                        <label>Certificate File:</label>
                                                        <input type="file" class="form-control"
                                                            @change="$wire.upload(`certificate_files.${index}`, $event.target.files[0])">

                                                        <!-- Preview Button for Existing Certificate -->
                                                        <template x-if="edu.existing_file">
                                                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                                                @click="window.open(getPdfUrl(), '_blank')">
                                                                Preview PDF
                                                            </button>
                                                        </template>

                                                        <div wire:loading wire:target="certificate_files"
                                                            class="text-primary mt-1">
                                                            <small>Uploading...</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end" x-show="educationLevels.length > 1">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeEducationLevel(index)">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-secondary"
                                        wire:click="previousStep">Back</button>
                                    @if (!$this->isViewMode)
                                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="submitForm">Submit</span>
                                            <span wire:loading wire:target="submitForm">
                                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                                Saving...
                                            </span>
                                        </button>
                                    @endif
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
    {{-- <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize Select2 on email dropdown
            $('#select-user').select2();
            $('#select-user').on('change', function(e) {
                @this.set('email', $(this).val());
                @this.dispatch('user-selected');
            });

        });
        document.addEventListener('DOMContentLoaded', () => {

            // Initialize Select2 on bank dropdown
            $('#select-bank').select2();
            $('#select-bank').on('change', function(e) {
                @this.set('bank_name', $(this).val());
            });

            // Initialize Select2 on role dropdown
            $('#selected-role').select2();
            $('#selected-role').on('change', function(e) {
                @this.set('selectedRole', $(this).val());
            });

            // Initialize Select2 on location dropdown
            $('#select-location').select2();
            $('#select-location').on('change', function(e) {
                @this.set('location', $(this).val());
            });

            // Initialize Select2 on unit dropdown
            $('#select-unit').select2();
            $('#select-unit').on('change', function(e) {
                @this.set('unit', $(this).val());
            });

            // Initialize Select2 on department dropdown
            $('#select-department').select2();
            $('#select-department').on('change', function(e) {
                @this.set('department', $(this).val());
            });

            // Listen for eventEmail to update Select2
            Livewire.on('eventEmail', (data) => {
                $('#select-user').val(data[0].employee_id).trigger('change');
                $('#select-role').val(data[0].employee_role).trigger('change');
                $('#select-bank').val(data[0].bank_name).trigger('change');
                $('#select-location').val(data[0].location_id).trigger('change');
                $('#select-unit').val(data[0].unit_id).trigger('change');
                $('#select-department').val(data[0].deparmtent_id).trigger('change');
            });

            // Reset file inputs when needed
            Livewire.on('resetFileState', () => {
                ['zan_id_file', 'nida_file'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
            });
        });
    </script> --}}

    @script
        <script>
            document.addEventListener('livewire:initialized', () => {

                const initSelect2Fields = () => {
                    const selects = [{
                            id: '#select-user',
                            field: 'email',
                            event: 'user-selected'
                        },
                        {
                            id: '#select-bank',
                            field: 'bank_name'
                        },
                        {
                            id: '#selected-role',
                            field: 'selectedRole'
                        },
                        {
                            id: '#select-location',
                            field: 'location'
                        },
                        {
                            id: '#select-unit',
                            field: 'unit'
                        },
                        {
                            id: '#select-department',
                            field: 'department'
                        }
                    ];

                    selects.forEach(select => {
                        let $el = $(select.id);
                        if ($el.length) {
                            $el.select2();
                            // .off().on() prevents duplicate listeners
                            $el.off('change').on('change', function(e) {
                                $wire.set(select.field, e.target.value);
                                if (select.event) $wire.dispatch(select.event);
                            });
                        }
                    });
                }

                // 1. Initial Load
                initSelect2Fields();

                // 2. Re-init when Livewire updates the DOM (Crucial for Steps/Edit)
                Livewire.hook('morph.updated', (el, component) => {
                    initSelect2Fields();
                });

                // 3. Update Visuals when Edit Data is loaded
                Livewire.on('eventEmail', (data) => {

                    // Handle both object and array formats
                    const row = Array.isArray(data) ? data[0] : data;

                    $('#select-user').val(row.employee_id).trigger('change');
                    $('#selected-role').val(row.employee_role).trigger('change');
                    $('#select-bank').val(row.bank_name).trigger('change');
                    $('#select-location').val(row.location_id).trigger('change');
                    $('#select-unit').val(row.unit_id).trigger('change');
                    $('#select-department').val(row.department_id).trigger('change');
                });
            });
        </script>
    @endscript
@endpush
