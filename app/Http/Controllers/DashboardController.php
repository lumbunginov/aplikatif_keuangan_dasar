<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($month);
        $userId = auth()->id();

        $totalIncome = Transaction::forUser($userId)->byMonth($date->year, $date->month)->income()->sum('amount');
        $totalExpense = Transaction::forUser($userId)->byMonth($date->year, $date->month)->expense()->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        $totalWalletBalance = Wallet::active()->forUser($userId)->sum('balance');

        $recentTransactions = Transaction::with(['category', 'wallet'])
            ->forUser($userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'month', 'totalIncome', 'totalExpense', 'netBalance',
            'totalWalletBalance', 'recentTransactions'
        ));
    }
}
