<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function preview($id, Request $request)
    {
        $attendance = Attendance::whereId($id)->first();

        // Base columns — Bank Account is always shown, same as
        // Position/From/Signature (a blank printed column, not stored data).
        $englishColumns = ['No.', 'Name', 'Position', 'From', 'Signature'];
        $swahiliColumns = ['No.', 'Jina', 'Cheo', 'Unapotoka', 'Saini'];

        // Phone No. is optional, mirroring AttendanceCreateEdit::getColumnsProperty()
        if ($attendance->include_phone_number) {
            $englishColumns[] = 'Bank Account';
            $swahiliColumns[] = 'Akaunti ya Benki.';
        }

        $englishColumns = array_merge($englishColumns, ['Bank Account']);
        $swahiliColumns = array_merge($swahiliColumns, ['Akaunti ya Benki']);

        // Pick the array dynamically based on the database column value
        $columns = $attendance->is_swahili === true ? $swahiliColumns : $englishColumns;

        $data = [
            'attendance' => $attendance,
            'title' => $attendance->title,
            'heading' => $attendance->heading,
            'meeting_date' => $attendance->meeting_date
                ? \Carbon\Carbon::parse($attendance->meeting_date)->format('d M, Y')
                : null,
            'columns' => $columns,
            'rows' => $attendance->number_of_rows,
            'isSwahili' => $attendance->is_swahili,
        ];

        $pdf = Pdf::loadView('attendance.preview', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('attendance-' . $attendance->id . '.pdf');
    }
}
