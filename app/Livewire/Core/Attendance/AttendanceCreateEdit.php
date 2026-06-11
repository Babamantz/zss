<?php

namespace App\Livewire\Core\Attendance;

use App\Models\Attendance;
use Livewire\Component;

class AttendanceCreateEdit extends Component
{
    public $attendanceId;
    public $default_lang = 'en';
    public $enable_english = true;
    public $enable_swahili = false;
    public $title_en = '';
    public $title_sw = '';
    public $heading_en = '';
    public $heading_sw = '';
    public $number_of_rows = 10;
    public $isEditMode = false;
    public $isViewMode = false;

    // Fixed column headers
    public $columnsEn = ['No.', 'Name', 'Position', 'From', 'Signature'];
    public $columnsSw = ['No.', 'Jina', 'Cheo', 'Unapotoka', 'Saini'];

    protected function rules()
    {
        $rules = [
            'default_lang' => 'required|in:en,sw',
            'number_of_rows' => 'required|integer|min:1|max:100',
        ];

        // Require at least one language
        if (!$this->enable_english && !$this->enable_swahili) {
            $rules['enable_english'] = 'accepted';
        }

        // Validate enabled languages
        if ($this->enable_english) {
            $rules['title_en'] = 'required|string|max:255';
            $rules['heading_en'] = 'required|string';
        }

        if ($this->enable_swahili) {
            $rules['title_sw'] = 'required|string|max:255';
            $rules['heading_sw'] = 'required|string';
        }

        return $rules;
    }

    protected $messages = [
        'enable_english.accepted' => 'At least one language must be enabled',
        'title_en.required' => 'English title is required when English is enabled',
        'title_sw.required' => 'Swahili title is required when Swahili is enabled',
        'heading_en.required' => 'English heading is required when English is enabled',
        'heading_sw.required' => 'Swahili heading is required when Swahili is enabled',
        'number_of_rows.required' => 'Number of rows is required',
        'number_of_rows.min' => 'Number of rows must be at least 1',
        'number_of_rows.max' => 'Number of rows cannot exceed 100',
    ];

    public function mount($attendanceId = null, $mode = null)
    {
        $this->attendanceId = $attendanceId;
        $this->isEditMode = !is_null($attendanceId);
        $this->isViewMode = ($mode === 'view');

        if ($attendanceId) {
            $this->loadAttendance();
        }
    }

    public function loadAttendance()
    {
        $attendance = Attendance::where('user_id', auth()->id())->findOrFail($this->attendanceId);

        $this->default_lang = $attendance->default_lang;
        $this->enable_english = in_array('en', $attendance->languages);
        $this->enable_swahili = in_array('sw', $attendance->languages);
        $this->title_en = $attendance->title['en'] ?? '';
        $this->title_sw = $attendance->title['sw'] ?? '';
        $this->heading_en = $attendance->heading['en'] ?? '';
        $this->heading_sw = $attendance->heading['sw'] ?? '';
        $this->number_of_rows = $attendance->number_of_rows;
    }

    public function submitForm()
    {
        $this->validate();

        try {
            $languages = [];
            if ($this->enable_english) $languages[] = 'en';
            if ($this->enable_swahili) $languages[] = 'sw';

            // Ensure default language is enabled
            if (!in_array($this->default_lang, $languages)) {
                $this->default_lang = $languages[0];
            }

            $data = [
                'user_id' => auth()->id(),
                'languages' => $languages,
                'default_lang' => $this->default_lang,
                'title' => [],
                'heading' => [],
                'number_of_rows' => $this->number_of_rows,
            ];

            // Only save data for enabled languages
            if ($this->enable_english) {
                $data['title']['en'] = $this->title_en;
                $data['heading']['en'] = $this->heading_en;
            }

            if ($this->enable_swahili) {
                $data['title']['sw'] = $this->title_sw;
                $data['heading']['sw'] = $this->heading_sw;
            }

            if ($this->isEditMode) {
                $attendance = Attendance::where('user_id', auth()->id())->findOrFail($this->attendanceId);
                $attendance->update($data);
                session()->flash('message', 'Attendance form template updated successfully.');
            } else {
                Attendance::create($data);
                session()->flash('message', 'Attendance form template created successfully.');
            }

            return redirect()->route('attendance.index');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updatedEnableEnglish()
    {
        // If disabling English and it's the default, switch to Swahili
        if (!$this->enable_english && $this->default_lang === 'en') {
            if ($this->enable_swahili) {
                $this->default_lang = 'sw';
            }
        }
    }

    public function updatedEnableSwahili()
    {
        // If disabling Swahili and it's the default, switch to English
        if (!$this->enable_swahili && $this->default_lang === 'sw') {
            if ($this->enable_english) {
                $this->default_lang = 'en';
            }
        }
    }

    public function render()
    {
        return view('livewire.core.attendance.attendance-create-edit');
    }
}
