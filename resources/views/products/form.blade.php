<div class="mb-3">
  <label class="form-label">商品名</label>
  <input type="text" name="product_name" class="form-control"
    value="{{ old('product_name',$product->product_name ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">価格</label>
  <input type="number" name="price" class="form-control"
    value="{{ old('price',$product->price ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">在庫数</label>
  <input type="number" name="stock" class="form-control"
    value="{{ old('stock',$product->stock ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">コメント</label>
  <textarea name="comment" class="form-control">{{ old('comment',$product->comment ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label class="form-label">商品画像</label>
  <input type="file" name="img" class="form-control">
</div>