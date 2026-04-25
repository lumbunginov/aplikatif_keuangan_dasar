<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@aplikatif.com')->first();
        if (! $admin) {
            return;
        }

        $wallets = $this->createWallets($admin->id);
        $this->createTransactions($admin->id, $wallets);
        $this->recalculateBalances($wallets);
    }

    private function createWallets(int $userId): array
    {
        $data = [
            ['name' => 'Kas Tunai', 'type' => 'cash', 'description' => 'Uang kas di tangan'],
            ['name' => 'Rekening BCA', 'type' => 'bank', 'description' => 'Rekening bank utama'],
            ['name' => 'Dana', 'type' => 'ewallet', 'description' => 'Dompet digital Dana'],
        ];

        $wallets = [];
        foreach ($data as $item) {
            $wallets[$item['type']] = Wallet::firstOrCreate(
                ['user_id' => $userId, 'name' => $item['name']],
                array_merge($item, ['user_id' => $userId, 'balance' => 0, 'is_active' => true])
            );
        }

        return $wallets;
    }

    private function createTransactions(int $userId, array $wallets): void
    {
        $categories = Category::whereNull('user_id')->pluck('id', 'name');

        $today = Carbon::today();

        // 3 months of transactions
        for ($m = 2; $m >= 0; $m--) {
            $month = $today->copy()->subMonths($m);
            $this->seedMonth($userId, $wallets, $categories, $month);
        }
    }

    private function seedMonth(int $userId, array $wallets, $categories, Carbon $month): void
    {
        $year = $month->year;
        $mon  = $month->month;
        $daysInMonth = $month->daysInMonth;

        $cash   = $wallets['cash'];
        $bank   = $wallets['bank'];
        $ewallet = $wallets['ewallet'];

        $transactions = [
            // === INCOME ===
            // Modal awal (only first month)
            ...($mon === Carbon::today()->subMonths(2)->month && $year === Carbon::today()->subMonths(2)->year ? [[
                'wallet' => $bank, 'category' => 'Modal Awal', 'type' => 'income',
                'amount' => 10000000, 'description' => 'Modal awal usaha',
                'day' => 1,
            ]] : []),

            // Penjualan produk — weekly
            ['wallet' => $cash, 'category' => 'Penjualan Produk', 'type' => 'income',
                'amount' => rand(800000, 1500000), 'description' => 'Penjualan produk minggu 1', 'day' => rand(1, 7)],
            ['wallet' => $cash, 'category' => 'Penjualan Produk', 'type' => 'income',
                'amount' => rand(900000, 1800000), 'description' => 'Penjualan produk minggu 2', 'day' => rand(8, 14)],
            ['wallet' => $bank, 'category' => 'Penjualan Produk', 'type' => 'income',
                'amount' => rand(1200000, 2500000), 'description' => 'Penjualan produk minggu 3', 'day' => rand(15, 21)],
            ['wallet' => $bank, 'category' => 'Penjualan Produk', 'type' => 'income',
                'amount' => rand(1000000, 2000000), 'description' => 'Penjualan produk minggu 4', 'day' => rand(22, min(28, $daysInMonth))],

            // Penjualan jasa
            ['wallet' => $bank, 'category' => 'Penjualan Jasa', 'type' => 'income',
                'amount' => rand(500000, 1500000), 'description' => 'Jasa konsultasi', 'day' => rand(5, 15)],
            ['wallet' => $ewallet, 'category' => 'Penjualan Jasa', 'type' => 'income',
                'amount' => rand(300000, 800000), 'description' => 'Jasa pengiriman', 'day' => rand(16, 25)],

            // Pendapatan lainnya
            ['wallet' => $ewallet, 'category' => 'Pendapatan Lainnya', 'type' => 'income',
                'amount' => rand(100000, 300000), 'description' => 'Cashback Dana', 'day' => rand(1, $daysInMonth)],

            // === EXPENSE ===
            // Bahan baku — awal bulan
            ['wallet' => $cash, 'category' => 'Bahan Baku', 'type' => 'expense',
                'amount' => rand(500000, 1000000), 'description' => 'Pembelian bahan baku', 'day' => rand(1, 5)],
            ['wallet' => $bank, 'category' => 'Bahan Baku', 'type' => 'expense',
                'amount' => rand(400000, 900000), 'description' => 'Restok bahan produksi', 'day' => rand(12, 18)],

            // Biaya operasional
            ['wallet' => $cash, 'category' => 'Biaya Operasional', 'type' => 'expense',
                'amount' => rand(100000, 350000), 'description' => 'Perlengkapan kantor', 'day' => rand(3, 10)],
            ['wallet' => $bank, 'category' => 'Biaya Operasional', 'type' => 'expense',
                'amount' => rand(200000, 500000), 'description' => 'Biaya cetak & packaging', 'day' => rand(11, 20)],

            // Gaji karyawan — akhir bulan
            ['wallet' => $bank, 'category' => 'Gaji Karyawan', 'type' => 'expense',
                'amount' => 2500000, 'description' => 'Gaji karyawan bulan ' . $month->translatedFormat('F Y'),
                'day' => min(25, $daysInMonth)],

            // Sewa tempat — awal bulan
            ['wallet' => $bank, 'category' => 'Sewa Tempat', 'type' => 'expense',
                'amount' => 1500000, 'description' => 'Sewa tempat usaha', 'day' => 1],

            // Listrik & Air
            ['wallet' => $bank, 'category' => 'Listrik & Air', 'type' => 'expense',
                'amount' => rand(150000, 350000), 'description' => 'Tagihan listrik & air', 'day' => rand(10, 15)],

            // Internet
            ['wallet' => $ewallet, 'category' => 'Internet & Komunikasi', 'type' => 'expense',
                'amount' => 350000, 'description' => 'Paket internet bisnis', 'day' => rand(1, 5)],

            // Transportasi
            ['wallet' => $cash, 'category' => 'Transportasi', 'type' => 'expense',
                'amount' => rand(50000, 200000), 'description' => 'Ongkos kirim & bensin', 'day' => rand(1, 10)],
            ['wallet' => $cash, 'category' => 'Transportasi', 'type' => 'expense',
                'amount' => rand(50000, 150000), 'description' => 'Biaya pengiriman produk', 'day' => rand(11, 20)],
            ['wallet' => $ewallet, 'category' => 'Transportasi', 'type' => 'expense',
                'amount' => rand(30000, 100000), 'description' => 'Ojek online', 'day' => rand(21, min(28, $daysInMonth))],

            // Marketing
            ['wallet' => $ewallet, 'category' => 'Biaya Marketing', 'type' => 'expense',
                'amount' => rand(100000, 500000), 'description' => 'Iklan media sosial', 'day' => rand(1, 10)],
            ['wallet' => $bank, 'category' => 'Biaya Marketing', 'type' => 'expense',
                'amount' => rand(200000, 600000), 'description' => 'Desain & konten promosi', 'day' => rand(11, 20)],

            // Pengeluaran lainnya
            ['wallet' => $cash, 'category' => 'Pengeluaran Lainnya', 'type' => 'expense',
                'amount' => rand(20000, 150000), 'description' => 'Biaya tak terduga', 'day' => rand(1, $daysInMonth)],
        ];

        foreach ($transactions as $t) {
            $date = Carbon::create($year, $mon, $t['day']);

            // Skip future dates
            if ($date->isFuture()) {
                continue;
            }

            $categoryId = $categories->get($t['category']);

            Transaction::create([
                'user_id'          => $userId,
                'wallet_id'        => $t['wallet']->id,
                'category_id'      => $categoryId,
                'type'             => $t['type'],
                'amount'           => $t['amount'],
                'description'      => $t['description'],
                'transaction_date' => $date->toDateString(),
            ]);
        }
    }

    private function recalculateBalances(array $wallets): void
    {
        foreach ($wallets as $wallet) {
            $income  = Transaction::where('wallet_id', $wallet->id)->where('type', 'income')->sum('amount');
            $expense = Transaction::where('wallet_id', $wallet->id)->where('type', 'expense')->sum('amount');
            $wallet->update(['balance' => $income - $expense]);
        }
    }
}
