<?php

namespace App\Livewire\Core\Attendance;

use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDir = 'desc';

    public $confirmingDeletion = false;
    public $attendanceToDelete = null;

    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function confirmDelete($attendanceId)
    {
        $this->attendanceToDelete = $attendanceId;
        $this->confirmingDeletion = true;
    }

    public function deleteAttendance()
    {
        if ($this->attendanceToDelete) {
            $attendance = Attendance::find($this->attendanceToDelete);

            // Check if user owns this form
            if ($attendance && $attendance->user_id === auth()->id()) {
                // Delete file from storage if exists
                if ($attendance->attendance_path && Storage::disk('public')->exists($attendance->attendance_path)) {
                    Storage::disk('public')->delete($attendance->attendance_path);
                }

                // Delete database record
                $attendance->delete();

                session()->flash('message', 'Attendance form deleted successfully.');
            } else {
                session()->flash('error', 'You are not authorized to delete this form.');
            }
        }

        $this->confirmingDeletion = false;
        $this->attendanceToDelete = null;
        $this->resetPage();
    }

    public function render()
    {
        $attendances = Attendance::query()
            ->where('user_id', auth()->id()) // Only show user's own forms
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $userId = auth()->id();

        $stats = [
            'total_forms'      => Attendance::where('user_id', $userId)->count(),
            'total_rows'       => Attendance::where('user_id', $userId)->sum('number_of_rows'),
            'forms_this_month' => Attendance::where('user_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('livewire.core.attendance.attendance-index', [
            'attendances' => $attendances,
            'stats'       => $stats,
        ]);
    }
}
