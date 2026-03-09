<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        if ($request->filled('causer')) {
            $query->where('causer_id', $request->causer);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('event')) {
            $query->where('description', 'like', '%' . $request->event . '%');
        }

        $activities = $query->latest()->paginate(25)->withQueryString();

        return view('activity-log.index', compact('activities'));
    }
}
