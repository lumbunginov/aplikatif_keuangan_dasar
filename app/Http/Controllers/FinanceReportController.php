<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($month);
        $userId = auth()->id();

        $transactions = Transaction::with('category')
            ->forUser($userId)
            ->byMonth($date->year, $date->month)
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $byCategory = $transactions->groupBy(fn($t) => $t->category?->name ?? 'Tanpa Kategori');

        $categoryData = [];
        foreach ($byCategory as $categoryName => $items) {
            $type = $items->first()->type;
            $total = $items->sum('amount');
            $categoryData[] = [
                'name' => $categoryName,
                'type' => $type,
                'total' => $total,
                'color' => $items->first()->category?->color ?? '#64748b',
            ];
        }

        return view('reports.index', compact('month', 'totalIncome', 'totalExpense', 'netBalance', 'categoryData'));
    }
}
