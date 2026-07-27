<?php

namespace App\Livewire\Core\Attendance;

use Livewire\Component;
use App\Models\Attendance;

class AttendanceCreateEdit extends Component
{
    public $attendanceId;
    public $title = '';
    public $heading = '';
    public $number_of_rows = 10;
    public $language = 'en'; // FIX: Tracks selected language code ('en' or 'sw')
    public $isEditMode = false;
    public $isViewMode = false;

    // Fixed column references
    public $englishColumns = ['No.', 'Name', 'Position', 'From', 'Signature'];
    public $swahiliColumns = ['No.', 'Jina', 'Cheo', 'Unapotoka', 'Saini'];

    // Dynamic getter for columns based on current state
    public function getColumnsProperty()
    {
        return $this->language === 'sw' ? $this->swahiliColumns : $this->englishColumns;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'heading' => 'required|string',
            'number_of_rows' => 'required|integer|min:1|max:100',
            'language' => 'required|in:en,sw', // FIX: Validates state values
        ];
    }

    protected $messages = [
        'title.required' => 'Title is required',
        'heading.required' => 'Heading is required',
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
        $this->title = $attendance->title;
        $this->heading = $attendance->heading;
        $this->number_of_rows = $attendance->number_of_rows;

        // FIX: Map database boolean column back into language select status code string
        $this->language = $attendance->is_swahili ? 'sw' : 'en';
    }

    public function submitForm()
    {
        $this->validate();
        try {
            $data = [
                'user_id' => auth()->id(),
                'title' => $this->title,
                'heading' => $this->heading,
                'number_of_rows' => $this->number_of_rows,
                'is_swahili' => $this->language === 'sw', // FIX: Transverts string back into DB boolean structure
            ];
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

    public function render()
    {
        return view('livewire.core.attendance.attendance-create-edit', [
            'columns' => $this->columns // FIX: Passes computed language headers directly to layout loop
        ]);
    }
}


