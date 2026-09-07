<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('transactions');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->orderBy('code', 'asc')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'              => 'required|string|max:50|unique:categories,code',
            'name'              => 'required|string|max:255',
            'type'              => 'required|in:income,expense',
            'is_tax_deductible' => 'required|boolean',
            'description'       => 'nullable|string',
        ]);

        Category::create([
            'code'              => $request->code,
            'name'              => $request->name,
            'type'              => $request->type,
            'is_tax_deductible' => (bool) $request->is_tax_deductible,
            'description'       => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Akun Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'code'              => 'required|string|max:50|unique:categories,code,' . $category->id,
            'name'              => 'required|string|max:255',
            'type'              => 'required|in:income,expense',
            'is_tax_deductible' => 'required|boolean',
            'description'       => 'nullable|string',
        ]);

        $category->update([
            'code'              => $request->code,
            'name'              => $request->name,
            'type'              => $request->type,
            'is_tax_deductible' => (bool) $request->is_tax_deductible,
            'description'       => $request->description,
        ]);

        return redirect()->route('categories.index')->with('success', 'Akun Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->transactions()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', "Kategori [{$category->code}] {$category->name} tidak dapat dihapus karena telah digunakan pada {$category->transactions()->count()} transaksi.");
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Akun Kategori berhasil dihapus.');
    }
}
