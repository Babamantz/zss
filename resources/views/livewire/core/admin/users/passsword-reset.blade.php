@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/.assets/css/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('./assets/css/select2.css') }}">
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Reset Password</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="resetPassword">
                        <div class="tab">
                            <div class="mb-2" wire:ignore>
                                <label class="col-form-label">Email</label>
                                <select class="js-example-basic-single col-sm-12" id="email-select"
                                    wire:model.defer="userId" required>

                                    <option value="">--select user--</option>
                                    @forelse ($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->email }}</option>
                                    @empty
                                        <option disabled>No role found</option>
                                    @endforelse
                                </select>
                                @error('userId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="col-form-label">Password</label>
                                <input class="form-control" type="password" wire:model.defer="password" id="password"
                                    required>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <div class="text-end btn-mb">
                                    <button class="btn btn-primary" type="submit">reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

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
            const elRole = $("#email-select");

            // Initialize Select2 with better options
            elRole.select2({
                placeholder: "-- Select Email --"
            });



            // Sync role changes with Livewire
            elRole.on('change', function() {
                @this.set('userId', $(this).val());
            });

            Livewire.on('resetEmail', (values) => {
                elRole.val(values).trigger('change.select2');
            });


        });
    </script>
@endpush
