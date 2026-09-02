@extends('layouts.app')
@section('title', 'Editar producto')
@section('content')
<h1>Editar producto</h1><form class="panel" method="post" action="{{ route('products.update', $product) }}">@csrf @method('put') @include('products.form')</form>
@endsection
