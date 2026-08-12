<?php

namespace App\Livewire\Core\Attendance;

use App\Models\Attendance;
use Illuminate\Container\Attributes\CurrentUser;
use Livewire\Component;
use App\Models\User;

class AttendanceCreateEdit extends Component
{
    public $attendanceId;
    public $title = '';
    public $heading = '';
    public $number_of_rows = 10;
    public $meeting_date;

    public bool $isSwahili = false;

    // Phone number is optional on the printed sheet — Bank Account is
    // always shown, so it doesn't need its own toggle.
    public bool $include_phone_number = false;

    public $isEditMode = false;
    public $isViewMode = false;

    /**
     * Get columns according to selected language, always including Bank
     * Account and optionally including Phone No. based on the creator's
     * choice.
     */
    public function getColumnsProperty()
    {
        $columns = $this->isSwahili
            ? ['No.', 'Jina', 'Cheo','Unapotoka','Sign']
            : ['No.', 'Name', 'Position','From','Signature'];

        if ($this->include_phone_number) {
            $columns[] = $this->isSwahili ? 'Akaunti ya Benki' : 'Bank Account';
        }

        return array_merge(
            $columns,
            $this->isSwahili
                ? ['Namba ya Simu']
                : ['Phone No.']
        );
    }

    protected function rules()
    {
        return [
            'title'                 => 'required|string|max:255',
            'heading'               => 'required|string',
            'meeting_date'          => 'required|date',
            'number_of_rows'        => 'required|integer|min:1|max:100',
            'isSwahili'             => 'required|boolean',
            'include_phone_number'  => 'boolean',
        ];
    }

    protected $messages = [
        'title.required' => 'Title is required',
        'heading.required' => 'Heading is required',
        'meeting_date.required' => 'Date of the meeting is required',
        'meeting_date.date' => 'Date of the meeting must be a valid date',
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
        $attendance = Attendance::where('user_id', auth()->id())
            ->findOrFail($this->attendanceId);

        $this->title = $attendance->title;
        $this->heading = $attendance->heading;
        $this->number_of_rows = $attendance->number_of_rows;
        $this->meeting_date = $attendance->meeting_date
            ? \Carbon\Carbon::parse($attendance->meeting_date)->format('Y-m-d')
            : null;

        // Database values are boolean.
        $this->isSwahili = (bool) $attendance->is_swahili;
        $this->include_phone_number = (bool) $attendance->include_phone_number;
    }

    public function submitForm(#[CurrentUser] User $user)
    {
        $this->validate();

        try {
            $data = [
                'user_id' => auth()->id(),
                'title' => $this->title,
                'heading' => $this->heading,
                'meeting_date' => $this->meeting_date,
                'number_of_rows' => $this->number_of_rows,
                'unit_id' => $user->employee?->unit?->id ?? null,
                'division_id' => $user->employee?->division?->id ?? null,
                'created_by' => $user->id,
                'is_swahili' => (bool) $this->isSwahili,
                'include_phone_number' => (bool) $this->include_phone_number,
            ];

            if ($this->isEditMode) {
                $attendance = Attendance::where('user_id', auth()->id())
                    ->findOrFail($this->attendanceId);

                $attendance->update($data);

                session()->flash(
                    'message',
                    'Attendance form template updated successfully.'
                );
            } else {
                Attendance::create($data);

                session()->flash(
                    'message',
                    'Attendance form template created successfully.'
                );
            }

            return redirect()->route('attendance.index');
        } catch (\Exception $e) {
            session()->flash(
                'error',
                'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.core.attendance.attendance-create-edit', [
            'columns' => $this->columns,
        ]);
    }
}
