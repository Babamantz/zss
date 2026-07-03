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

    @push('scripts')
        <script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('assets/js/editor/ckeditor/adapters/jquery.js') }}"></script>
        <script>
            function attendanceForm() {
                return {
                    editor: null,
                    isViewMode: {{ $isViewMode ? 'true' : 'false' }},

                    init() {
                        this.$nextTick(() => {
                            this.initEditor();
                        });

                        Livewire.on('livewire:updated', () => {
                            this.$nextTick(() => {
                                if (!this.editor) {
                                    this.initEditor();
                                }
                            });
                        });
                    },

                    initEditor() {
                        const elementId = 'heading_editor';

                        if (CKEDITOR.instances[elementId]) {
                            CKEDITOR.instances[elementId].destroy(true);
                        }

                        if (!document.getElementById(elementId)) {
                            return;
                        }

                        const editor = CKEDITOR.replace(elementId, {
                            height: 300,
                            readOnly: this.isViewMode
                        });

                        this.editor = editor;

                        editor.on('instanceReady', () => {
                            editor.setData(@json($heading ?? ''));
                        });

                        if (!this.isViewMode) {
                            editor.on('change', () => {
                                this.$wire.set('heading', editor.getData());
                            });

                            editor.on('blur', () => {
                                this.$wire.set('heading', editor.getData());
                            });
                        }
                    },

                    destroyEditors() {
                        if (typeof CKEDITOR !== 'undefined') {
                            for (let instance in CKEDITOR.instances) {
                                CKEDITOR.instances[instance].destroy(true);
                            }
                        }
                    }
                }
            }
        </script>
    @endpush
</div>