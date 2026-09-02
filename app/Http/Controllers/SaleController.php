<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::with(['user', 'customer'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        $products = Product::where('active', true)->where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => $products,
            'productOptions' => $products->map(fn ($product) => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => (float) $product->sale_price,
                'stock' => $product->stock,
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:efectivo,transferencia,tarjeta,mixto'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $discount = (float) ($data['discount'] ?? 0);

        try {
            $sale = DB::transaction(function () use ($data, $discount) {
                $subtotal = 0;
                $items = [];

                foreach ($data['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if (! $product->active || $product->stock < $item['quantity']) {
                        throw new \RuntimeException("Stock insuficiente para {$product->name}.");
                    }

                    $lineSubtotal = (float) $product->sale_price * (int) $item['quantity'];
                    $subtotal += $lineSubtotal;
                    $items[] = [$product, (int) $item['quantity'], $lineSubtotal];
                }

                if ($discount > $subtotal) {
                    throw new \RuntimeException('El descuento no puede ser mayor que el subtotal.');
                }

                $total = $subtotal - $discount;

                if ((float) $data['paid_amount'] < $total) {
                    throw new \RuntimeException('El valor pagado es menor que el total.');
                }

                $sale = Sale::create([
                    'invoice_number' => 'FV-'.now()->format('YmdHis').'-'.auth()->id(),
                    'user_id' => auth()->id(),
                    'customer_id' => $data['customer_id'] ?? null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => 0,
                    'total' => $total,
                    'paid_amount' => $data['paid_amount'],
                    'change_amount' => (float) $data['paid_amount'] - $total,
                    'payment_method' => $data['payment_method'],
                ]);

                foreach ($items as [$product, $quantity, $lineSubtotal]) {
                    $sale->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->sale_price,
                        'quantity' => $quantity,
                        'subtotal' => $lineSubtotal,
                    ]);

                    $product->decrement('stock', $quantity);
                }

                return $sale;
            });
        } catch (\RuntimeException $exception) {
            return back()->withInput()->withErrors($exception->getMessage());
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Venta registrada.');
    }

    public function show(Sale $sale)
    {
        return view('sales.show', ['sale' => $sale->load(['items', 'user', 'customer'])]);
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status === 'anulada') {
            return back()->withErrors('La venta ya esta anulada.');
        }

        DB::transaction(function () use ($sale) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $item->product?->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'anulada']);
        });

        return back()->with('success', 'Venta anulada y stock restaurado.');
    }
}
