@extends('layouts.app')

@section('content')
<div class="container">
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <h1>商品編集</h1>
  <form action="{{ route('products.update',$product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('products.partials.form')
    <button class="btn btn-warning">更新</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
  </form>
</div>
@endsection