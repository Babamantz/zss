@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select.css') }}">
    <!-- Optional: include when using Bootstrap 4 theme styling -->
    <link rel="stylesheet" href="{{ asset('vendor/async-select/async-select-bootstrap-v4.css') }}">
@endpush

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h5 class="fw-bold text-dark">Reset User Password</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="resetPassword">
                        <div
                            class="section-title mb-4 pb-2 border-bottom text-muted small text-uppercase fw-semibold tracking-wider">
                            Account Security Credentials
                        </div>

                        <div class="row g-4">
                            {{-- User Email Selection --}}
                            <div class="col-md-6" wire:ignore>
                                <label class="form-label fw-medium" for="email-select">User Email Address</label>
                                <livewire:async-select name="users" wire:model="userId" :options="$this->users"
                                    placeholder="Select users..." />
                                @error('userId') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- New Password Input Field --}}
                            <div class="col-md-6">
                                <label class="form-label fw-medium" for="password">New Secure Password</label>
                                <input class="form-control" type="password" wire:model.defer="password" id="password"
                                    required>
                                @error('password') <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Bar: Unified Navigation Ergonomics --}}
                        <div class="action-bar d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-dark">
                                <i class="fa fa-arrow-left me-1"></i> Back to Users
                            </a>
                            <div>
                                <button class="btn btn-outline-secondary me-2" type="reset"> Cancel </button>
                                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-4">
                                    <i class="fa fa-key me-1"></i> Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush