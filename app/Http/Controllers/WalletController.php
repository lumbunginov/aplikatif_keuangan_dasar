<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::forUser(auth()->id())->withCount('transactions')->get();
        $totalBalance = $wallets->where('is_active', true)->sum('balance');

        return view('wallets.index', compact('wallets', 'totalBalance'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:cash,bank,ewallet,other',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Wallet::create($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function edit(Wallet $wallet)
    {
        abort_unless($wallet->user_id === auth()->id(), 403);

        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        abort_unless($wallet->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:cash,bank,ewallet,other',
            'description' => 'nullable|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $wallet->update($validated);

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet)
    {
        abort_unless($wallet->user_id === auth()->id(), 403);

        if ($wallet->transactions()->exists()) {
            return back()->with('error', 'Dompet tidak bisa dihapus karena masih memiliki transaksi.');
        }

        $wallet->delete();

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dihapus.');
    }
}
