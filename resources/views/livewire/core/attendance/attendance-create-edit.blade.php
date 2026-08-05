{{-- resources/views/livewire/core/attendance/attendance-create-edit.blade.php --}}

<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header pb-0">
                        <h5>
                            {{ $isEditMode
                                ? ($isViewMode ? 'View Attendance Form' : 'Edit Attendance Form')
                                : 'Create Attendance Form' }}
                        </h5>
                    </div>

                    <div class="card-body add-post">

                        {{-- Success Message --}}
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}

                                <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                                </button>
                            </div>
                        @endif

                        {{-- Error Message --}}
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}

                                <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                                </button>
                            </div>
                        @endif

                        <form
                            id="attendance-form"
                            class="row needs-validation"
                            wire:submit.prevent="submitForm"
                            novalidate
                        >

                            <div class="col-sm-12">

                                {{-- Number of Rows --}}
                                <div class="form-group">

                                    <label for="number_of_rows">
                                        Number of Attendee Rows
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        class="form-control @error('number_of_rows') is-invalid @enderror"
                                        id="number_of_rows"
                                        type="number"
                                        wire:model.live="number_of_rows"
                                        min="1"
                                        max="100"
                                        {{ $isViewMode ? 'disabled' : '' }}
                                        placeholder="Enter number of rows (1-100)"
                                    >

                                    <small class="text-muted">
                                        This determines how many people can sign the attendance sheet
                                    </small>

                                    @error('number_of_rows')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <hr>

                                {{-- Title --}}
                                <div class="form-group">

                                    <label for="title">
                                        Title
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        class="form-control @error('title') is-invalid @enderror"
                                        id="title"
                                        type="text"
                                        wire:model="title"
                                        {{ $isViewMode ? 'disabled' : '' }}
                                        placeholder="e.g., Monthly Staff Meeting Attendance"
                                    >

                                    @error('title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                {{-- Heading with CKEditor --}}
                                <div class="email-wrapper">

                                    <div class="theme-form">

                                        <div class="form-group">

                                            <label for="heading_editor">
                                                Heading
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div wire:ignore>
                                                <textarea
                                                    id="heading_editor"
                                                    name="heading"
                                                    cols="10"
                                                    rows="2"
                                                    {{ $isViewMode ? 'disabled' : '' }}
                                                >{{ $heading }}</textarea>
                                            </div>

                                            @error('heading')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>

                                </div>

                                {{-- Language --}}
                                <div class="form-group mb-3">

                                    <label for="language">
                                        Template Column Language
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="language"
                                            name="language"
                                            wire:model.live="isSwahili"
                                            {{ $isViewMode ? 'disabled' : '' }}
                                        >

                                        <label class="form-check-label" for="language">
                                            Kiswahili
                                        </label>
                                    </div>

                                    @error('isSwahili')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                {{-- Fixed Columns Preview --}}
                                <div class="form-group">

                                    <label class="d-block">
                                        Table Columns (Fixed) -
                                        Preview with {{ $number_of_rows }} rows
                                    </label>

                                    <div class="table-responsive">

                                        <table class="table table-bordered table-sm">

                                            <thead class="table-light">
                                                <tr>

                                                    @foreach ($columns as $column)
                                                        <th>
                                                            {{ $column }}
                                                        </th>
                                                    @endforeach

                                                </tr>
                                            </thead>

                                            <tbody>

                                                @for ($i = 1; $i <= min((int) $number_of_rows, 5); $i++)

                                                    <tr>

                                                        @foreach ($columns as $index => $column)

                                                            @if ($index === 0)
                                                                <td>
                                                                    {{ $i }}
                                                                </td>
                                                            @else
                                                                <td></td>
                                                            @endif

                                                        @endforeach

                                                    </tr>

                                                @endfor

                                                @if ((int) $number_of_rows > 5)

                                                    <tr>
                                                        <td
                                                            colspan="{{ count($columns) }}"
                                                            class="text-center text-muted"
                                                        >
                                                            <em>
                                                                ...
                                                                and
                                                                {{ $number_of_rows - 5 }}
                                                                more rows
                                                            </em>
                                                        </td>
                                                    </tr>

                                                @endif

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                            {{-- Action Buttons --}}
                            <div class="btn-showcase mt-3">

                                <a
                                    href="{{ route('attendance.index') }}"
                                    class="btn btn-light"
                                    wire:navigate
                                >
                                    Back
                                </a>

                                @if (!$isViewMode)

                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        wire:loading.attr="disabled"
                                    >

                                        <span
                                            wire:loading.remove
                                            wire:target="submitForm"
                                        >
                                            {{ $isEditMode ? 'Update' : 'Create' }}
                                        </span>

                                        <span
                                            wire:loading
                                            wire:target="submitForm"
                                        >
                                            <span
                                                class="spinner-border spinner-border-sm"
                                                role="status"
                                            ></span>

                                            Saving...
                                        </span>

                                    </button>

                                @endif

                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- CKEditor --}}
    <script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>

    <script>
        function initializeCKEditor() {

            const textarea = document.getElementById('heading_editor');

            if (!textarea) {
                return;
            }

            if (typeof CKEDITOR === 'undefined') {
                return;
            }

            // Prevent duplicate instances
            if (CKEDITOR.instances.heading_editor) {
                CKEDITOR.instances.heading_editor.destroy(true);
            }

            // Find Livewire component
            const component = textarea.closest('[wire\\:id]');

            if (!component) {
                return;
            }

            const wireId = component.getAttribute('wire:id');
            const wire = Livewire.find(wireId);

            if (!wire) {
                return;
            }

            const editor = CKEDITOR.replace('heading_editor', {
                height: 300,
                removeButtons: '',
                readOnly: textarea.disabled
            });

            // Load current Livewire value
            editor.on('instanceReady', function () {

                let content = '';

                try {
                    content = wire.get('heading') || '';
                } catch (e) {
                    content = textarea.value || '';
                }

                editor.setData(content);
            });

            // Sync editor -> Livewire
            editor.on('change', function () {

                if (!textarea.disabled) {
                    wire.set('heading', editor.getData());
                }

            });

            editor.on('blur', function () {

                if (!textarea.disabled) {
                    wire.set('heading', editor.getData());
                }

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Initial Page Load
        |--------------------------------------------------------------------------
        */

        document.addEventListener('DOMContentLoaded', function () {

            setTimeout(() => {
                initializeCKEditor();
            }, 50);

        });


        /*
        |--------------------------------------------------------------------------
        | Livewire Navigate
        |--------------------------------------------------------------------------
        */

        document.addEventListener('livewire:navigated', function () {

            setTimeout(() => {
                initializeCKEditor();
            }, 50);

        });


        /*
        |--------------------------------------------------------------------------
        | Livewire Initialization
        |--------------------------------------------------------------------------
        */

        document.addEventListener('livewire:init', function () {

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


        /*
        |--------------------------------------------------------------------------
        | Cleanup Before Navigation
        |--------------------------------------------------------------------------
        */

        document.addEventListener('livewire:navigate', function () {

            if (
                typeof CKEDITOR !== 'undefined' &&
                CKEDITOR.instances.heading_editor
            ) {
                CKEDITOR.instances.heading_editor.destroy(true);
            }

        });
    </script>

</div>