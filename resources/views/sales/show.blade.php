@extends('layouts.app')
@section('title', 'Factura')
@section('content')
<div class="actions" style="justify-content:space-between;margin-bottom:14px;"><h1>Factura {{ $sale->invoice_number }}</h1><a class="btn light" href="{{ route('sales.index') }}">Volver</a></div>
<div class="panel">
    <p><strong>Cliente:</strong> {{ $sale->customer->name ?? 'Consumidor final' }}</p>
    <p><strong>Vendedor:</strong> {{ $sale->user->name }} | <strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }} | <strong>Estado:</strong> {{ $sale->status }}</p>
    <table><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>
    @foreach($sale->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->unit_price, 0) }}</td><td>${{ number_format($item->subtotal, 0) }}</td></tr>@endforeach
    </tbody></table>
    <div style="max-width:360px;margin-left:auto;margin-top:16px;">
        <p><strong>Subtotal:</strong> ${{ number_format($sale->subtotal, 0) }}</p>
        <p><strong>Descuento:</strong> ${{ number_format($sale->discount, 0) }}</p>
        <p><strong>Total:</strong> ${{ number_format($sale->total, 0) }}</p>
        <p><strong>Pagado:</strong> ${{ number_format($sale->paid_amount, 0) }}</p>
        <p><strong>Cambio:</strong> ${{ number_format($sale->change_amount, 0) }}</p>
    </div>
    @if($sale->status !== 'anulada')
    <form method="post" action="{{ route('sales.destroy', $sale) }}">@csrf @method('delete')<button class="btn danger" onclick="return confirm('Anular esta venta?')">Anular venta</button></form>
    @endif
</div>
@endsection
