<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'supplier'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create', $this->formData());
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', $this->formData() + compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists()) {
            return back()->withErrors('No se puede eliminar un producto vendido.');
        }

        $product->delete();

        return back()->with('success', 'Producto eliminado.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('active', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'code' => ['required', 'string', 'max:80', 'unique:products,code,'.($product?->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => false];
    }
}
