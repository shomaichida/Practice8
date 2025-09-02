@extends('layouts.app')

@section('content')
<div class="container">
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <h1>商品新規登録</h1>
  <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('products.partials.form')
    <button class="btn btn-primary">登録</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
  </form>
</div>
@endsection