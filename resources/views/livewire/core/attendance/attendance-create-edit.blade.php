{{-- resources/views/livewire/attendance/attendance-form.blade.php --}}
<div x-data="attendanceForm()" x-init="init()">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>{{ $isEditMode ? ($isViewMode ? 'View Attendance Form' : 'Edit Attendance Form') : 'Create Attendance Form' }}
                        </h5>
                    </div>

                    <div class="card-body add-post">
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form class="row needs-validation" wire:submit.prevent="submitForm" novalidate>
                            <div class="col-sm-12">

                                {{-- Number of Rows --}}
                                <div class="form-group">
                                    <label for="number_of_rows">Number of Attendee Rows <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control @error('number_of_rows') is-invalid @enderror"
                                        id="number_of_rows" type="number" wire:model="number_of_rows" min="1" max="100"
                                        {{ $isViewMode ? 'disabled' : '' }} placeholder="Enter number of rows (1-100)">
                                    <small class="text-muted">This determines how many people can sign the attendance
                                        sheet</small>
                                    @error('number_of_rows')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                {{-- Title --}}
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input class="form-control @error('title') is-invalid @enderror" id="title"
                                        type="text" wire:model="title" {{ $isViewMode ? 'disabled' : '' }}
                                        placeholder="e.g., Monthly Staff Meeting Attendance">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Heading with CKEditor --}}
                                <div class="email-wrapper">
                                    <div class="theme-form">
                                        <div class="form-group">
                                            <label>Heading <span class="text-danger">*</span></label>
                                            <div wire:ignore>
                                                <textarea id="heading_editor" name="heading" cols="10"
                                                    rows="2">{{ $heading }}</textarea>
                                            </div>
                                            @error('heading')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Fixed Columns Preview --}}
                                <div class="form-group">
                                    <label class="d-block">Table Columns (Fixed) - Preview with
                                        {{ $number_of_rows }} rows</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    @foreach ($columns as $column)
                                                        <th>{{ $column }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= min($number_of_rows, 5); $i++)
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endfor
                                                @if ($number_of_rows > 5)
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            <em>... and {{ $number_of_rows - 5 }} more
                                                                rows</em>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </form>

                        {{-- Action Buttons --}}
                        <div class="btn-showcase mt-3">
                            <a href="{{ route('attendance.index') }}" class="btn btn-light" wire:navigate>Back</a>

                            @if (!$isViewMode)
                                <button class="btn btn-primary" type="submit" wire:click="submitForm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitForm">
                                        {{ $isEditMode ? 'Update' : 'Create' }}
                                    </span>
                                    <span wire:loading wire:target="submitForm">
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                        Saving...
                                    </span>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


   

    <script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>

    <script>
        function initializeCKEditor() {

            const textarea = document.getElementById('heading_editor');

            // Editor not on page
            if (!textarea) {
                return;
            }

            // Prevent duplicate instances
            if (CKEDITOR.instances.heading_editor) {
                CKEDITOR.instances.heading_editor.destroy(true);
            }

            // Get Livewire component
            const component = textarea.closest('[wire\\:id]');

            if (!component) {
                return;
            }

            const wire = Livewire.find(component.getAttribute('wire:id'));

            // Create editor
            const editor = CKEDITOR.replace('heading_editor', {
                height: 300,
                removeButtons: '',
                readOnly: textarea.disabled
            });

            // Load current value from Livewire
            editor.on('instanceReady', function () {

                let content = '';

                try {
                    content = wire.get('heading') || '';
                } catch (e) {
                    content = textarea.value || '';
                }

                editor.setData(content);
            });

            // Sync changes to Livewire
            editor.on('change', function () {
                wire.set('heading', editor.getData());
            });

            editor.on('blur', function () {
                wire.set('heading', editor.getData());
            });
        }

        // Initial page load
        document.addEventListener('DOMContentLoaded', function () {
            initializeCKEditor();
        });

        // Livewire Navigate
        document.addEventListener('livewire:navigated', function () {
            setTimeout(() => {
                initializeCKEditor();
            }, 50);
        });

        // Livewire component refresh/update
        document.addEventListener('livewire:init', () => {

            Livewire.hook('morph.updated', () => {

                const editorExists =
                    typeof CKEDITOR !== 'undefined' &&
                    CKEDITOR.instances.heading_editor;

                if (!editorExists) {
                    setTimeout(() => {
                        initializeCKEditor();
                    }, 50);
                }
            });
        });

        // Cleanup before navigating away
        document.addEventListener('livewire:navigate', () => {

            if (
                typeof CKEDITOR !== 'undefined' &&
                CKEDITOR.instances.heading_editor
            ) {
                CKEDITOR.instances.heading_editor.destroy(true);
            }
        });
    </script>
</div>