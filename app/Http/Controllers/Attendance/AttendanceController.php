<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function preview($id, Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())->findOrFail($id);

        $data = [
            'attendance' => $attendance,
            'title' => $attendance->title,
            'heading' => $attendance->heading,
            'columns' => ['No.', 'Name', 'Position', 'From', 'Signature'],
            'rows' => $attendance->number_of_rows,
        ];

        $pdf = Pdf::loadView('attendance.preview', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('attendance-' . $attendance->id . '.pdf');
    }
}
