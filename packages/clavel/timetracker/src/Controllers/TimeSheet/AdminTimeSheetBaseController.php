<?php

namespace Clavel\TimeTracker\Controllers\TimeSheet;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Clavel\TimeTracker\Models\TimeSheet;
use App\Http\Controllers\AdminController;

class AdminTimeSheetController extends AdminController
{
    protected function save(Request $request, TimeSheet $timesheet, $user_id)
    {
        try {
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $request->input("start_time", ""));
            $timesheet->start_time = $startDate;
        } catch (\Exception $err) {
            $startDate = Carbon::now();
            $timesheet->start_time = $startDate;
        }

        if (!empty($request->input("end_time", ""))) {
            try {
                $endDate =  Carbon::createFromFormat('d/m/Y H:i', $request->input("end_time", ""));
                $timesheet->end_time = $endDate;
                $timesheet->duration = $endDate->diffInSeconds($startDate);
            } catch (\Exception $err) {
                $timesheet->end_time = null;
                $timesheet->duration = null;
            }
        } else {
            $timesheet->end_time = null;
            $timesheet->duration = null;
        }

        if (!empty($request->input("description", ""))) {
            $timesheet->description = $request->input("description", "");
        } else {
            $timesheet->description = '';
        }

        $timesheet->project_id = $request->input("project_id", "");
        $timesheet->activity_id = $request->input("activity_id", "");
        $timesheet->user_id = $user_id;
        $timesheet->fixed_rate = $request->input("fixed_rate", "");
        $timesheet->hourly_rate = $request->input("hourly_rate", "");

        $timesheet->save();
    }
}
