@extends('layouts.app')
@section('title', 'Nueva venta')
@section('content')
<h1>Nueva venta</h1>
<form class="panel" method="post" action="{{ route('sales.store') }}" id="saleForm">
    @csrf
    <div class="form-grid">
        <label>Cliente<select name="customer_id"><option value="">Consumidor final</option>@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->document ? '- '.$customer->document : '' }}</option>@endforeach</select></label>
        <label>Metodo de pago<select name="payment_method" required><option value="efectivo">Efectivo</option><option value="transferencia">Transferencia</option><option value="tarjeta">Tarjeta</option><option value="mixto">Mixto</option></select></label>
    </div>
    <h2 style="margin-top:18px;">Productos</h2>
    <div id="items" class="grid"></div>
    <button class="btn light" type="button" onclick="addItem()">Agregar producto</button>
    <div class="form-grid" style="margin-top:18px;">
        <label>Descuento<input type="number" step="0.01" min="0" name="discount" id="discount" value="0" oninput="calculate()"></label>
        <label>Valor pagado<input type="number" step="0.01" min="0" name="paid_amount" id="paid" value="0" oninput="calculate()" required></label>
    </div>
    <div class="grid grid-4" style="margin-top:16px;">
        <div class="panel"><div class="muted">Subtotal</div><div class="metric" id="subtotal">$0</div></div>
        <div class="panel"><div class="muted">Total</div><div class="metric" id="total">$0</div></div>
        <div class="panel"><div class="muted">Cambio</div><div class="metric" id="change">$0</div></div>
        <div class="panel"><div class="muted">Lineas</div><div class="metric" id="lines">0</div></div>
    </div>
    <div class="actions" style="margin-top:16px;"><button class="btn">Registrar venta</button><a class="btn light" href="{{ route('sales.index') }}">Cancelar</a></div>
</form>
<script>
const products = @json($productOptions);
let index = 0;
function money(value) { return '$' + Math.round(value).toLocaleString('es-CO'); }
function addItem() {
    const row = document.createElement('div');
    row.className = 'form-grid';
    row.innerHTML = `<label>Producto<select name="items[${index}][product_id]" onchange="calculate()" required><option value="">Seleccione</option>${products.map(p => `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">${p.code} - ${p.name} ($${Math.round(p.price).toLocaleString('es-CO')}, stock ${p.stock})</option>`).join('')}</select></label><label>Cantidad<input type="number" min="1" name="items[${index}][quantity]" value="1" oninput="calculate()" required></label><button class="btn danger" type="button" onclick="this.parentElement.remove();calculate()">Quitar</button>`;
    document.getElementById('items').appendChild(row);
    index++;
    calculate();
}
function calculate() {
    let subtotal = 0;
    document.querySelectorAll('#items .form-grid').forEach(row => {
        const select = row.querySelector('select');
        const quantity = parseInt(row.querySelector('input').value || '0', 10);
        const price = parseFloat(select.selectedOptions[0]?.dataset.price || '0');
        subtotal += price * quantity;
    });
    const discount = parseFloat(document.getElementById('discount').value || '0');
    const total = Math.max(subtotal - discount, 0);
    const paid = parseFloat(document.getElementById('paid').value || '0');
    document.getElementById('subtotal').textContent = money(subtotal);
    document.getElementById('total').textContent = money(total);
    document.getElementById('change').textContent = money(Math.max(paid - total, 0));
    document.getElementById('lines').textContent = document.querySelectorAll('#items .form-grid').length;
}
addItem();
</script>
@endsection
