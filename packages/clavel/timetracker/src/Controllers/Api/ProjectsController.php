<?php

namespace Clavel\TimeTracker\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Clavel\TimeTracker\Models\Project;
use App\Http\Controllers\ApiController;

class ProjectsController extends ApiController
{
    public function getProjects(Request $request, $id)
    {
        if (!Auth::user()->can('admin-timesheet-list') && !Auth::user()->can('admin-mytimes-list')) {
            app()->abort(403);
        }

        $projects = Project::actives()->where('customer_id', $id)->pluck('name', 'id')->all();
        return response()->json($projects);
    }
}
