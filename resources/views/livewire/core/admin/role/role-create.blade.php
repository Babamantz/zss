@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/.assets/css/select2.css') }}">
@endpush
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Admin User Create</h5>
                    <a class="btn btn-primary" href="{{ route('roles.index') }}">Back</a>
                </div>
                <div class="card-body">
                    <form class="" id="" action="#" method="POST">
                        <div class="tab">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <input class="form-control digits" id="role" type="text" wire:model=''>
                            </div>
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
                                <input class="form-control digits" id="permissions" type="text" wire:model=''>
                            </div>
                        </div>
                </div>
                <div>
                    <div class="text-end btn-mb">
                        <button class="btn btn-primary" type="submit">create</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
