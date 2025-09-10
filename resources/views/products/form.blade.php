@if ($errors->any())
<div class="alert alert-danger">
  <ul class="mb-0">
    @foreach ($errors->all() as $e)
    <li>{{ $e }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="mb-3">
  <label class="form-label">商品名</label>
  <input type="text" name="product_name"
         value="{{ old('product_name', $product->product_name ?? '') }}"
         class="form-control @error('product_name') is-invalid @enderror">
  @error('product_name')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label">価格</label>
  <input type="number" name="price" min="0" step="1"
         value="{{ old('price', $product->price ?? '') }}"
         class="form-control @error('price') is-invalid @enderror">
  @error('price')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label">在庫数</label>
  <input type="number" name="stock" min="0" step="1"
         value="{{ old('stock', $product->stock ?? '') }}"
         class="form-control @error('stock') is-invalid @enderror">
  @error('stock')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label">コメント</label>
  <textarea name="comment" rows="3"
            class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $product->comment ?? '') }}</textarea>
  @error('comment')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label">商品画像</label>
  <input type="file" name="img" accept="image/*"
         class="form-control @error('img') is-invalid @enderror">
  @error('img')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>