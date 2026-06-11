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
    public $langFilter = 'all';
    public $confirmingDeletion = false;
    public $attendanceToDelete = null;

    protected $queryString = ['search', 'langFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLangFilter()
    {
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
    }

    public function render()
    {
        $attendances = Attendance::query()
            ->where('user_id', auth()->id()) // Only show user's own forms
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title->en', 'like', '%' . $this->search . '%')
                        ->orWhere('title->sw', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->langFilter !== 'all', function ($query) {
                $query->whereJsonContains('languages', $this->langFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.core.attendance.attendance-index', [
            'attendances' => $attendances
        ]);
    }
}
