@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select.css') }}">
    <!-- Optional: include when using Bootstrap 4 theme styling -->
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select-bootstrap-v4.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $employeeId ? 'Edit Employee' : 'Create Employee' }}</h5>
                    <a class="btn btn-primary btn-sm" href="{{ route('hrm.employees.index') }}">Back</a>
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

                    {{-- ── Step Indicator ──────────────────────────────────────── --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            @foreach ([1 => 'Basic Info', 2 => 'Employment', 3 => 'IDs', 4 => 'Certificates', 5 => 'Education'] as $step => $label)
                                <div class="text-center {{ $currentStep >= $step ? 'text-primary' : 'text-muted' }}">
                                    <div class="mb-2">
                                        <span class="badge {{ $currentStep >= $step ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-3">
                                            {{ $step }}
                                        </span>
                                    </div>
                                    <small>{{ $label }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form wire:submit.prevent="submitForm">
                        <input type="hidden" wire:model="userId">

                        {{-- ══════════════════════════════════════════════════════
                             STEP 1 — Basic Information
                        ══════════════════════════════════════════════════════ --}}
                        <div @if($currentStep != 1) style="display:none;" @endif>
                            <h5 class="mb-3">Basic Information</h5>

                            {{-- Email + First Name --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="select-user">Email: <span class="text-danger">*</span></label>
                                        <livewire:async-select
                                            name="email"
                                            wire:model.live="email"
                                            :options="$this->emails"
                                            placeholder="Select email..."
                                        />
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" wire:model.defer="first_name">
                                        @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Middle Name + Last Name --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Middle Name:</label>
                                        <input class="form-control" type="text" wire:model.defer="middle_name">
                                        @error('middle_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" wire:model.defer="last_name">
                                        @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- DOB + Hired Date --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date of Birth: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" wire:model.defer="dob">
                                        @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Age: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" wire:model.defer="age">
                                        {{-- @error('dob') <small class="text-danger">{{ $message }}</small> @enderror --}}
                                    </div>
                                </div>
                            </div>
                                <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hired Date: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" wire:model.defer="hired_date">
                                        @error('hired_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Work Confirmation Date: <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" wire:model.defer="work_confirmation_date">
                                        @error('work_confirmation_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Retiring Date + Education Level --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Retiring Date:</label>
                                        <input class="form-control" type="date" wire:model.defer="retiring_date">
                                        @error('retiring_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label>Education Level: <span class="text-danger">*</span></label>
                                        <livewire:async-select
                                            name="education_level_id"
                                            wire:model="education_level_id"
                                            :options="$this->educationLevels"
                                            placeholder="Select education level..."
                                        />
                                        @error('education_level_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>
                              {{-- Contacts --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Personal Phone: <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control"
                                            wire:model.defer="contacts.0.phone_number"
                                            placeholder="+255 7XX XXX XXX" 
                                        @error('contacts.0.phone_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Next of Kin Phone:</label>
                                        <input type="tel" class="form-control"
                                            wire:model.defer="contacts.1.phone_number"
                                            placeholder="+255 7XX XXX XXX"
                                        @error('contacts.1.phone_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                         

                            {{-- Marital Status + Gender --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Marital Status: <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            @foreach (['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced'] as $val => $label)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model.defer="marital_status"
                                                        value="{{ $val }}" id="ms_{{ $val }}">
                                                    <label class="form-check-label" for="ms_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('marital_status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender: <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            @foreach (['male' => 'Male', 'female' => 'Female'] as $val => $label)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model.defer="gender"
                                                        value="{{ $val }}" id="gender_{{ $val }}">
                                                    <label class="form-check-label" for="gender_{{ $val }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Disability + Employment Type --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Has Disability?</label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    wire:model.defer="is_disable" value="0" id="disable_no">
                                                <label class="form-check-label" for="disable_no">No</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    wire:model.defer="is_disable" value="1" id="disable_yes">
                                                <label class="form-check-label" for="disable_yes">Yes</label>
                                            </div>
                                        </div>
                                        @error('is_disable') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div  class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label>Disability Type: <span class="text-danger"></span></label>
                                        <livewire:async-select
                                            name="disable_types"
                                            wire:model="disable_types"
                                            :options="$this->disabilityTypes"
                                            placeholder="Select Disability Type..."
                                              multiple="true"
                                        />
                                    </div>
                                    @error('disable_types') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                
                            </div>
                             <div class="row mb-3">
                                <div wire:ignore class="col-md-6">
                                    <div class="form-group">
                                        <label>Employment Type: <span class="text-danger">*</span></label>
                                        <livewire:async-select
                                            name="employment_type_id"
                                            wire:model="employment_type_id"
                                            :options="$this->employmentTypes"
                                            placeholder="Select employment type..."
                                        />
                                    </div>
                                @error('employment_type_id') <small class="text-danger">{{ $message }}</small> @enderror

                                </div>
                                
                             </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════
                             STEP 2 — Employment Details
                        ══════════════════════════════════════════════════════ --}}
                        <div @if($currentStep != 2) style="display:none;" @endif>
                            <h5 class="mb-3">Employment Details</h5>

                            {{-- OPF Number + Designation --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payroll (OPF) Number:</label>
                                        <input class="form-control" type="text" wire:model.defer="opf_number">
                                        @error('opf_number') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label>Designation: <span class="text-danger">*</span></label>
                                        <livewire:async-select
                                            name="designation_id"
                                            wire:model="designation_id"
                                            :options="$this->designations"
                                            placeholder="Select designations..."
                                        />
                                        @error('designation_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Department + Unit + Division --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department:</label>
                                        <input type="text" class="form-control" value="{{ $department }}" readonly placeholder="Auto-filled from Division">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div wire:ignore class="form-group">
                                        <label>Unit:</label>
                                        <livewire:async-select
                                            name="unit"
                                            wire:model="unit"
                                            :options="$this->units"
                                            placeholder="Select unit..."
                                        />
                                        @error('unit') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div wire:ignore class="form-group">
                                        <label>Division:</label>
                                        <livewire:async-select
                                            name="division"
                                            wire:model="divisionId"
                                            :options="$this->divisions"
                                            placeholder="Select divisions..."
                                        />
                                        @error('divisionId') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Bank + Bank Account No --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div wire:ignore class="form-group">
                                        <label>Bank:</label>
                                        <livewire:async-select
                                            name="banks"
                                            wire:model="employee_bank_id"
                                            :options="$this->banks"
                                            placeholder="Select bank..."
                                        />
                                        @error('employee_bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bank Account No:</label>
                                        <input class="form-control" type="text" wire:model.defer="employee_bank_account_no">
                                        @error('employee_bank_account_no') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- File Number + Photo --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>File Number:</label>
                                        <input class="form-control" type="text" wire:model.defer="file_number">
                                        @error('file_number') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Photo:</label>
                                        <input class="form-control" type="file" wire:model="photo_file"
                                            accept="image/jpg,image/jpeg,image/png">
                                        @error('photo_file') <small class="text-danger">{{ $message }}</small> @enderror
                                        <div wire:loading wire:target="photo_file" class="text-primary mt-1">
                                            <small>Uploading...</small>
                                        </div>
                                    </div>
                                    @if ($existing_photo_file)
                                        <div class="mt-2">
                                            <small class="text-muted">Current photo:</small><br>
                                            <img src="{{ Storage::disk('public')->url($existing_photo_file) }}"
                                                class="mt-1 rounded"
                                                style="width:60px;height:60px;object-fit:cover;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Birth Certificate + Employment Contract --}}
                            <div class="row mb-3">
                                <div class="col-md-6"
                                     x-data="{ url: '{{ $existing_birth_certificate_file ? Storage::disk('public')->url($existing_birth_certificate_file) : '' }}' }">
                                    <div class="form-group">
                                        <label>Birth Certificate:</label>
                                        <input class="form-control" type="file"
                                            wire:model="birth_certificate_file"
                                            accept="application/pdf,image/jpeg,image/png">
                                        @error('birth_certificate_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="birth_certificate_file" class="text-primary mt-1">
                                            <small>Uploading...</small>
                                        </div>
                                        <template x-if="url">
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                @click="window.open(url, '_blank')">
                                                Preview existing
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="col-md-6"
                                     x-data="{ url: '{{ $existing_employment_contract_file ? Storage::disk('public')->url($existing_employment_contract_file) : '' }}' }">
                                    <div class="form-group">
                                        <label>Employment Contract:</label>
                                        <input class="form-control" type="file"
                                            wire:model="employment_contract_file"
                                            accept="application/pdf,image/jpeg,image/png">
                                        @error('employment_contract_file')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <div wire:loading wire:target="employment_contract_file" class="text-primary mt-1">
                                            <small>Uploading...</small>
                                        </div>
                                        <template x-if="url">
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                @click="window.open(url, '_blank')">
                                                Preview existing
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════
                             STEP 3 — Identifications
                             Option A: server-rendered repeater. Each row is a real
                             Blade @foreach iteration, so <livewire:async-select>
                             gets its own component instance per row, keyed on the
                             row's stable id. No Alpine x-for / @entangle here —
                             every add/remove/edit is a normal Livewire round trip.
                        ══════════════════════════════════════════════════════ --}}
                        <div @if($currentStep != 3) style="display:none;" @endif>
                            <div class="pb-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Identification Documents</h5>
                                <button type="button" class="btn btn-success btn-sm"
                                    wire:click="addIdentificationItem">
                                    <i class="fa fa-plus"></i> Add Identification
                                </button>
                            </div>

                            @foreach ($identification_items as $index => $item)
                                <div class="card mb-3" wire:key="identification-row-{{ $item['key'] ?? $index }}">
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div wire:ignore class="form-group">
                                                    <label>Identification Type: <span class="text-danger">*</span></label>
                                                    <livewire:async-select
                                                        :key="'identification-type-'.($item['key'] ?? $index)"
                                                        name="identification_items.{{ $index }}.identification_id"
                                                        wire:model="identification_items.{{ $index }}.identification_id"
                                                        :options="$this->identificationTypes"
                                                        :selected="$item['identification_id'] ?? null"
                                                        placeholder="Select identification type..."
                                                    />
                                                    @error('identification_items.'.$index.'.identification_id')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Identification Number:</label>
                                                    <input type="text" class="form-control"
                                                        wire:model.defer="identification_items.{{ $index }}.identification_no">
                                                    @error('identification_items.'.$index.'.identification_no')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Identification File:</label>
                                                    <input type="file" class="form-control"
                                                        wire:model="identification_upload_files.{{ $index }}">
                                                    @if (!empty($item['existing_file']))
                                                        <a href="{{ Storage::disk('public')->url($item['existing_file']) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary mt-2">
                                                            Preview existing
                                                        </a>
                                                    @endif
                                                    <div wire:loading wire:target="identification_upload_files.{{ $index }}"
                                                        class="text-primary mt-1">
                                                        <small>Uploading...</small>
                                                    </div>
                                                    @error('identification_upload_files.'.$index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if (count($identification_items) > 1)
                                            <div class="text-end">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeIdentificationItem({{ $index }})">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════
                             STEP 4 — Certificates (file-type docs)
                             Same Option A pattern as Step 3.
                        ══════════════════════════════════════════════════════ --}}
                        <div @if($currentStep != 4) style="display:none;" @endif>
                            <div class="pb-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Certificate Documents</h5>
                                <button type="button" class="btn btn-success btn-sm"
                                    wire:click="addCertificateItem">
                                    <i class="fa fa-plus"></i> Add Certificate
                                </button>
                            </div>

                            @foreach ($certificate_items as $index => $item)
                                <div class="card mb-3" wire:key="certificate-row-{{ $item['key'] ?? $index }}">
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div wire:ignore class="form-group">
                                                    <label>Certificate Type: <span class="text-danger">*</span></label>
                                                    <livewire:async-select
                                                        :key="'certificate-type-'.($item['key'] ?? $index)"
                                                        name="certificate_items.{{ $index }}.certificate_id"
                                                        wire:model="certificate_items.{{ $index }}.certificate_id"
                                                        :options="$this->certificateTypes"
                                                        :selected="$item['certificate_id'] ?? null"
                                                        placeholder="Select certificate type..."
                                                    />
                                                    @error('certificate_items.'.$index.'.certificate_id')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Certificate Number:</label>
                                                    <input type="text" class="form-control"
                                                        wire:model.defer="certificate_items.{{ $index }}.certificate_no">
                                                    @error('certificate_items.'.$index.'.certificate_no')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Certificate File:</label>
                                                    <input type="file" class="form-control"
                                                        wire:model="certificate_upload_files.{{ $index }}">
                                                    @if (!empty($item['existing_file']))
                                                        <a href="{{ Storage::disk('public')->url($item['existing_file']) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary mt-2">
                                                            Preview existing
                                                        </a>
                                                    @endif
                                                    <div wire:loading wire:target="certificate_upload_files.{{ $index }}"
                                                        class="text-primary mt-1">
                                                        <small>Uploading...</small>
                                                    </div>
                                                    @error('certificate_upload_files.'.$index)
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if (count($certificate_items) > 1)
                                            <div class="text-end">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeCertificateItem({{ $index }})">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════
                             STEP 5 — Education Levels
                             (left as Alpine x-for / @entangle — unaffected by the
                             async-select-in-a-loop issue since these rows only use
                             plain inputs and x-model)
                        ══════════════════════════════════════════════════════ --}}
                        <div @if($currentStep != 5) style="display:none;" @endif>
                            <div x-data="{ educationLevelsRows: @entangle('education_levels') }">

                                <div class="pb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Education Details</h5>
                                    <button type="button" class="btn btn-success btn-sm"
                                        wire:click="addEducationLevel">
                                        <i class="fa fa-plus"></i> Add Education
                                    </button>
                                </div>

                                <template x-for="(edu, index) in educationLevelsRows" :key="index">
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
                                                        <label>Certificate Name: <span class="text-danger">*</span></label>
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
                                                <div class="col-md-6"
                                                     x-data="{ getPdfUrl() { return edu.existing_file ? '{{ Storage::disk('public')->url('') }}' + edu.existing_file : null } }">
                                                    <div class="form-group">
                                                        <label>Certificate File:</label>
                                                        <input type="file" class="form-control"
                                                            @change="$wire.upload(`certificate_files.${index}`, $event.target.files[0])">
                                                        <template x-if="edu.existing_file">
                                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                                @click="window.open(getPdfUrl(), '_blank')">
                                                                Preview existing
                                                            </button>
                                                        </template>
                                                        <div wire:loading wire:target="certificate_files"
                                                            class="text-primary mt-1">
                                                            <small>Uploading...</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end" x-show="educationLevelsRows.length > 1">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeEducationLevel(index)">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                                    @if (!$this->isViewMode)
                                        <button type="submit" class="btn btn-success"
                                            wire:loading.attr="disabled">
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