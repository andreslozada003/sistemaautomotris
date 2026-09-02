<div class="form-grid">
    <label>Codigo<input name="code" value="{{ old('code', $product->code ?? '') }}" required></label>
    <label>Nombre<input name="name" value="{{ old('name', $product->name ?? '') }}" required></label>
    <label>Categoria<select name="category_id" required><option value="">Seleccione</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <label>Proveedor<select name="supplier_id"><option value="">Sin proveedor</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></label>
    <label>Costo<input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required></label>
    <label>Precio venta<input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? 0) }}" required></label>
    <label>Stock<input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required></label>
    <label>Stock minimo<input type="number" min="0" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 1) }}" required></label>
    <label class="span-2">Descripcion<textarea name="description">{{ old('description', $product->description ?? '') }}</textarea></label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400;"><input type="checkbox" name="active" value="1" style="width:auto;" @checked(old('active', $product->active ?? true))> Activo</label>
</div><div class="actions" style="margin-top:14px;"><button class="btn">Guardar</button><a class="btn light" href="{{ route('products.index') }}">Cancelar</a></div>
