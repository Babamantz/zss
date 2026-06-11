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
                                {{-- Language Selection --}}
                                <div class="form-group">
                                    <label>Enable Languages <span class="text-danger">* (At least one
                                            required)</span></label>
                                    <div class="m-checkbox-inline">
                                        <label class="f-w-500" for="enable-en">
                                            <input class="checkbox_animated" id="enable-en" type="checkbox"
                                                wire:model.live="enable_english" x-on:change="reinitializeEditors()"
                                                {{ $isViewMode ? 'disabled' : '' }}>
                                            English
                                        </label>
                                        <label class="f-w-500" for="enable-sw">
                                            <input class="checkbox_animated" id="enable-sw" type="checkbox"
                                                wire:model.live="enable_swahili" x-on:change="reinitializeEditors()"
                                                {{ $isViewMode ? 'disabled' : '' }}>
                                            Swahili
                                        </label>
                                    </div>
                                    @error('enable_english')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Default Language --}}
                                @if ($enable_english || $enable_swahili)
                                    <div class="form-group">
                                        <label>Default Display Language <span class="text-danger">*</span></label>
                                        <div class="m-checkbox-inline">
                                            @if ($enable_english)
                                                <label class="f-w-500" for="lang-en">
                                                    <input class="radio_animated" id="lang-en" type="radio"
                                                        wire:model="default_lang" value="en"
                                                        {{ $isViewMode ? 'disabled' : '' }}>
                                                    English
                                                </label>
                                            @endif
                                            @if ($enable_swahili)
                                                <label class="f-w-500" for="lang-sw">
                                                    <input class="radio_animated" id="lang-sw" type="radio"
                                                        wire:model="default_lang" value="sw"
                                                        {{ $isViewMode ? 'disabled' : '' }}>
                                                    Swahili
                                                </label>
                                            @endif
                                        </div>
                                        @error('default_lang')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                {{-- Number of Rows --}}
                                <div class="form-group">
                                    <label for="number_of_rows">Number of Attendee Rows <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control @error('number_of_rows') is-invalid @enderror"
                                        id="number_of_rows" type="number" wire:model="number_of_rows" min="1"
                                        max="100" {{ $isViewMode ? 'disabled' : '' }}
                                        placeholder="Enter number of rows (1-100)">
                                    <small class="text-muted">This determines how many people can sign the attendance
                                        sheet</small>
                                    @error('number_of_rows')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                {{-- English Version --}}
                                @if ($enable_english)
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">English Version</h6>

                                        {{-- Title (English) --}}
                                        <div class="form-group">
                                            <label for="title_en">Title <span class="text-danger">*</span></label>
                                            <input class="form-control @error('title_en') is-invalid @enderror"
                                                id="title_en" type="text" wire:model="title_en"
                                                {{ $isViewMode ? 'disabled' : '' }}
                                                placeholder="e.g., Monthly Staff Meeting Attendance">
                                            @error('title_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Heading (English) with CKEditor --}}
                                        <div class="email-wrapper">
                                            <div class="theme-form">
                                                <div class="form-group">
                                                    <label>Heading <span class="text-danger">*</span></label>
                                                    <div wire:ignore>
                                                        <textarea id="heading_en_editor" name="heading_en" cols="10" rows="2">{{ $heading_en }}</textarea>
                                                    </div>
                                                    @error('heading_en')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Fixed Columns Preview (English) --}}
                                        <div class="form-group">
                                            <label class="d-block">Table Columns (Fixed) - Preview with
                                                {{ $number_of_rows }} rows</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            @foreach ($columnsEn as $column)
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
                                    <hr>
                                @endif

                                {{-- Swahili Version --}}
                                @if ($enable_swahili)
                                    <div class="mb-4">
                                        <h6 class="text-success mb-3">Swahili Version / Toleo la Kiswahili</h6>

                                        {{-- Title (Swahili) --}}
                                        <div class="form-group">
                                            <label for="title_sw">Kichwa / Title <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control @error('title_sw') is-invalid @enderror"
                                                id="title_sw" type="text" wire:model="title_sw"
                                                {{ $isViewMode ? 'disabled' : '' }}
                                                placeholder="mfano, Mahudhurio ya Mkutano wa Wafanyakazi wa Kila Mwezi">
                                            @error('title_sw')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Heading (Swahili) with CKEditor --}}
                                        <div class="email-wrapper">
                                            <div class="theme-form">
                                                <div class="form-group">
                                                    <label>Utangulizi / Heading <span
                                                            class="text-danger">*</span></label>
                                                    <div wire:ignore>
                                                        <textarea id="heading_sw_editor" name="heading_sw" cols="10" rows="2">{{ $heading_sw }}</textarea>
                                                    </div>
                                                    @error('heading_sw')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Fixed Columns Preview (Swahili) --}}
                                        <div class="form-group">
                                            <label class="d-block">Safu za Jedwali / Table Columns (Fixed) - Muhtasari
                                                wenye safu {{ $number_of_rows }}</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="table-light">
                                                        <tr>
                                                            @foreach ($columnsSw as $column)
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
                                                                    <em>... na safu {{ $number_of_rows - 5 }}
                                                                        zaidi</em>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
                    editors: {
                        en: null,
                        sw: null
                    },
                    isViewMode: {{ $isViewMode ? 'true' : 'false' }},
                    enableEnglish: {{ $enable_english ? 'true' : 'false' }},
                    enableSwahili: {{ $enable_swahili ? 'true' : 'false' }},

                    init() {
                        // Wait for Livewire to be initialized
                        this.$nextTick(() => {
                            this.initializeEditors();
                        });

                        // Listen for Livewire updates
                        Livewire.on('livewire:updated', () => {
                            this.$nextTick(() => {
                                this.initializeEditors();
                            });
                        });
                    },

                    initializeEditors() {
                        // Initialize English editor if enabled
                        if (this.enableEnglish && document.getElementById('heading_en_editor')) {
                            this.initEditor('en', 'heading_en_editor', 'heading_en', @json($heading_en ?? ''));
                        }

                        // Initialize Swahili editor if enabled
                        if (this.enableSwahili && document.getElementById('heading_sw_editor')) {
                            this.initEditor('sw', 'heading_sw_editor', 'heading_sw', @json($heading_sw ?? ''));
                        }
                    },

                    initEditor(lang, elementId, wireProperty, initialContent) {
                        // Destroy existing instance if exists
                        if (CKEDITOR.instances[elementId]) {
                            CKEDITOR.instances[elementId].destroy(true);
                        }

                        // Check if element exists
                        if (!document.getElementById(elementId)) {
                            console.log('Element not found:', elementId);
                            return;
                        }

                        // Create new editor
                        const editor = CKEDITOR.replace(elementId, {
                            height: 300,
                            readOnly: this.isViewMode
                        });

                        // Store reference
                        this.editors[lang] = editor;

                        // Set initial content when ready
                        editor.on('instanceReady', () => {
                            editor.setData(initialContent || '');
                            console.log('Editor initialized:', elementId);
                        });

                        // Sync with Livewire on change
                        if (!this.isViewMode) {
                            editor.on('change', () => {
                                this.$wire.set(wireProperty, editor.getData());
                            });

                            // Also sync on blur
                            editor.on('blur', () => {
                                this.$wire.set(wireProperty, editor.getData());
                            });
                        }
                    },

                    reinitializeEditors() {
                        // Wait for Livewire to update
                        setTimeout(() => {
                            // Update flags
                            this.enableEnglish = {{ $enable_english ? 'true' : 'false' }};
                            this.enableSwahili = {{ $enable_swahili ? 'true' : 'false' }};

                            // Re-initialize editors
                            this.initializeEditors();
                        }, 200);
                    },

                    destroyEditors() {
                        // Destroy all editor instances
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
