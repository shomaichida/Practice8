@extends('layouts.app')

@section('content')
<div class="container">
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <h1>商品詳細</h1>
  <div class="card p-3">
    <p>ID: {{ $product->id }}</p>
    <p>商品名: {{ $product->product_name }}</p>
    <p>価格: {{ $product->price }}</p>
    <p>在庫: {{ $product->stock }}</p>
    <p>コメント: {{ $product->comment }}</p>
    @if($product->img_path)
    <img src="{{ asset('storage/'.$product->img_path) }}" class="img-fluid mb-3" alt="">
    @endif
    <a href="{{ route('products.edit',$product) }}" class="btn btn-warning">編集</a>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
  </div>
</div>
@endsection