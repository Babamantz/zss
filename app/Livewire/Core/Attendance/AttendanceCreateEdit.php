<?php

namespace App\Livewire\Core\Attendance;

use App\Models\Attendance;
use Livewire\Component;

class AttendanceCreateEdit extends Component
{
    public $attendanceId;
    public $title = '';
    public $heading = '';
    public $number_of_rows = 10;
    public $isEditMode = false;
    public $isViewMode = false;

    // Fixed column headers
    public $columns = ['No.', 'Name', 'Position', 'From', 'Signature'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'heading' => 'required|string',
            'number_of_rows' => 'required|integer|min:1|max:100',
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
        return view('livewire.core.attendance.attendance-create-edit');
    }
}
