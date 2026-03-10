<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'wallet'])->forUser(auth()->id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }
        if ($request->filled('month')) {
            $date = \Carbon\Carbon::parse($request->month);
            $query->byMonth($date->year, $date->month);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        $wallets = Wallet::forUser(auth()->id())->get();
        $categories = Category::where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'wallets', 'categories'));
    }

    public function create()
    {
        $wallets = Wallet::active()->forUser(auth()->id())->get();
        $categories = Category::where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('type')->orderBy('name')->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|max:255',
            'notes' => 'nullable',
            'transaction_date' => 'required|date',
        ]);

        $wallet = Wallet::where('id', $validated['wallet_id'])->where('user_id', auth()->id())->firstOrFail();

        if ($validated['category_id']) {
            Category::where('id', $validated['category_id'])
                ->where(fn($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'))
                ->firstOrFail();
        }

        $validated['user_id'] = auth()->id();

        $transaction = Transaction::create($validated);

        $this->updateWalletBalance($wallet, $transaction->type, $transaction->amount);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $wallets = Wallet::active()->forUser(auth()->id())->get();
        $categories = Category::where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('type')->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'wallets', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|max:255',
            'notes' => 'nullable',
            'transaction_date' => 'required|date',
        ]);

        $wallet = Wallet::where('id', $validated['wallet_id'])->where('user_id', auth()->id())->firstOrFail();

        // Revert old balance
        $oldWallet = $transaction->wallet;
        $this->revertWalletBalance($oldWallet, $transaction->type, $transaction->amount);

        $validated['user_id'] = auth()->id();
        $transaction->update($validated);

        // Apply new balance
        $wallet->refresh();
        $this->updateWalletBalance($wallet, $transaction->type, $transaction->amount);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        $this->revertWalletBalance($transaction->wallet, $transaction->type, $transaction->amount);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    private function updateWalletBalance(Wallet $wallet, string $type, float $amount): void
    {
        if ($type === 'income') {
            $wallet->balance += $amount;
        } else {
            $wallet->balance -= $amount;
        }
        $wallet->save();
    }

    private function revertWalletBalance(Wallet $wallet, string $type, float $amount): void
    {
        if ($type === 'income') {
            $wallet->balance -= $amount;
        } else {
            $wallet->balance += $amount;
        }
        $wallet->save();
    }
}
