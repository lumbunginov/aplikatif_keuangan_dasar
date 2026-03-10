<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Income
            ['name' => 'Penjualan Produk', 'type' => 'income', 'color' => '#10b981'],
            ['name' => 'Penjualan Jasa', 'type' => 'income', 'color' => '#3b82f6'],
            ['name' => 'Modal Awal', 'type' => 'income', 'color' => '#8b5cf6'],
            ['name' => 'Pendapatan Lainnya', 'type' => 'income', 'color' => '#6366f1'],
            // Expense
            ['name' => 'Bahan Baku', 'type' => 'expense', 'color' => '#ef4444'],
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'color' => '#f97316'],
            ['name' => 'Biaya Marketing', 'type' => 'expense', 'color' => '#f59e0b'],
            ['name' => 'Gaji Karyawan', 'type' => 'expense', 'color' => '#eab308'],
            ['name' => 'Sewa Tempat', 'type' => 'expense', 'color' => '#84cc16'],
            ['name' => 'Listrik & Air', 'type' => 'expense', 'color' => '#22c55e'],
            ['name' => 'Internet & Komunikasi', 'type' => 'expense', 'color' => '#14b8a6'],
            ['name' => 'Transportasi', 'type' => 'expense', 'color' => '#06b6d4'],
            ['name' => 'Pajak', 'type' => 'expense', 'color' => '#0ea5e9'],
            ['name' => 'Pengeluaran Lainnya', 'type' => 'expense', 'color' => '#64748b'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'is_default' => true],
                array_merge($category, ['user_id' => null, 'is_default' => true])
            );
        }
    }
}
