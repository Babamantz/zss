<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function preview($id, Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->id())->findOrFail($id);

        // Get language from request or use default
        $lang = $request->get('lang', $attendance->default_lang);

        // Ensure requested language is available
        if (!in_array($lang, $attendance->languages)) {
            $lang = $attendance->default_lang;
        }

        return view('attendance.preview', [
            'attendance' => $attendance,
            'lang' => $lang,
            'title' => $attendance->getTitle($lang),
            'heading' => $attendance->getHeading($lang),
            'columns' => $attendance->getColumnHeaders($lang),
            'rows' => $attendance->number_of_rows,
        ]);
    }
}
