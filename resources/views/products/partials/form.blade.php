@if ($errors->any())
<div class="alert alert-danger">
  <strong>入力内容に誤りがあります。</strong>
  <ul class="mb-0">
    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
  </ul>
</div>
@endif

<div class="mb-3">
  <label class="form-label">商品名 <span class="text-danger">*</span></label>
  <input type="text" name="product_name" class="form-control"
    value="{{ old('product_name', $product->product_name ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">価格 <span class="text-danger">*</span></label>
  <input type="number" name="price" class="form-control" min="0" step="1"
    value="{{ old('price', $product->price ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">在庫数 <span class="text-danger">*</span></label>
  <input type="number" name="stock" class="form-control" min="0" step="1"
    value="{{ old('stock', $product->stock ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">メーカー名 <span class="text-danger">*</span></label>
  <select name="company_id" class="form-select" required>
    <option value="">選択してください</option>
    @foreach($companies as $c)
    <option value="{{ $c->id }}" @selected(old('company_id', $product->company_id ?? '') == $c->id)>
      {{ $c->company_name }}
    </option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label">コメント</label>
  <textarea name="comment" class="form-control" rows="3">{{ old('comment', $product->comment ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label class="form-label">商品画像</label>
  <input type="file" name="img" class="form-control" accept="image/*">
</div>