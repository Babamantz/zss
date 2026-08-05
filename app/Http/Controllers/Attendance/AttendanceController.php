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

        // Define your standard column arrays
        $englishColumns = ['No.', 'Name', 'Position', 'From', 'Signature'];
        $swahiliColumns = ['No.', 'Jina', 'Cheo', 'Unapotoka', 'Saini'];
        dd($attendance);

        // FIX: Pick the array dynamically based on the database column value
        $columns = $attendance->is_swahili === true ? $swahiliColumns : $englishColumns;

        dd($columns);

        $data = [
            'attendance' => $attendance,
            'title' => $attendance->title,
            'heading' => $attendance->heading,
            'columns' => $columns, // FIX: Pass the dynamic language column choices array
            'rows' => $attendance->number_of_rows,
            'isSwahili' => $attendance->is_swahili
        ];

        $pdf = Pdf::loadView('attendance.preview', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('attendance-' . $attendance->id . '.pdf');
    }
}

// class AttendanceController extends Controller
// {
//     public function preview($id, Request $request)
//     {
//         $attendance = Attendance::where('user_id', auth()->id())->findOrFail($id);

//         $data = [
//             'attendance' => $attendance,
//             'title' => $attendance->title,
//             'heading' => $attendance->heading,
//             'columns' => ['No.', 'Name', 'Position', 'From', 'Signature'],
//             'rows' => $attendance->number_of_rows,
//         ];

//         $pdf = Pdf::loadView('attendance.preview', $data)
//             ->setPaper('a4', 'landscape');

//         return $pdf->stream('attendance-' . $attendance->id . '.pdf');
//     }
// }
