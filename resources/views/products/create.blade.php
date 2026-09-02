@extends('layouts.app')
@section('title', 'Nuevo producto')
@section('content')
<h1>Nuevo producto</h1><form class="panel" method="post" action="{{ route('products.store') }}">@csrf @include('products.form')</form>
@endsection
