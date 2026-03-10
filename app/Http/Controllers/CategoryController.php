<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where(function ($q) {
            $q->where('user_id', auth()->id())->orWhereNull('user_id');
        })->orderBy('type')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:income,expense',
            'color' => 'required|max:7',
            'description' => 'nullable|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_default'] = false;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        abort_unless(
            $category->user_id === auth()->id() || (!$category->is_default),
            403
        );

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_unless(
            $category->user_id === auth()->id() || (!$category->is_default),
            403
        );

        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|in:income,expense',
            'color' => 'required|max:7',
            'description' => 'nullable|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->is_default) {
            return back()->with('error', 'Kategori default tidak bisa dihapus.');
        }

        abort_unless($category->user_id === auth()->id(), 403);

        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki transaksi.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
