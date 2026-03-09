<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $hour = (int) now()->format('H');
        if ($hour < 12) {
            $greeting = 'Selamat pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_roles' => Role::count(),
            'recent_activities' => Activity::count(),
        ];

        $recentActivities = Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact('greeting', 'stats', 'recentActivities'));
    }
}
