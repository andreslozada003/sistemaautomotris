@extends('layouts.app')
@section('title', 'Productos')
@section('content')
<div class="actions" style="justify-content:space-between;margin-bottom:14px;"><h1>Productos</h1><a class="btn" href="{{ route('products.create') }}">Nuevo producto</a></div>
<form class="actions" method="get" style="margin-bottom:14px;"><input name="search" placeholder="Buscar por nombre o codigo" value="{{ request('search') }}" style="max-width:360px;"><button class="btn light">Buscar</button></form>
<table><thead><tr><th>Codigo</th><th>Producto</th><th>Categoria</th><th>Stock</th><th>Precio</th><th></th></tr></thead><tbody>
@forelse($products as $product)
<tr><td>{{ $product->code }}</td><td>{{ $product->name }}</td><td>{{ $product->category->name }}</td><td>@if($product->stock <= $product->min_stock)<span class="badge warn">{{ $product->stock }}</span>@else {{ $product->stock }} @endif</td><td>${{ number_format($product->sale_price, 0) }}</td><td class="actions"><a class="btn light" href="{{ route('products.edit', $product) }}">Editar</a><form method="post" action="{{ route('products.destroy', $product) }}">@csrf @method('delete')<button class="btn danger" onclick="return confirm('Eliminar producto?')">Eliminar</button></form></td></tr>
@empty <tr><td colspan="6" class="muted">No hay productos.</td></tr> @endforelse
</tbody></table><div class="pagination">{{ $products->links() }}</div>
@endsection
