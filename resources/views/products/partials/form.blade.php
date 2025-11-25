@if ($errors->any())
<div class="alert alert-danger">
  <strong>入力内容に誤りがあります。</strong>
  <ul class="mb-0">
    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
  </ul>
</div>
@endif

{{-- 商品名 --}}
<div class="mb-3">
    <label class="form-label">商品名</label>
    <input type="text" name="product_name" class="form-control"
           value="{{ old('product_name', $product->product_name ?? '') }}">
    @error('product_name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- 価格 --}}
<div class="mb-3">
    <label class="form-label">価格</label>
    <input type="number" name="price" class="form-control" min="0" step="1"
           value="{{ old('price', $product->price ?? '') }}">
    @error('price')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- 在庫数 --}}
<div class="mb-3">
    <label class="form-label">在庫数</label>
    <input type="number" name="stock" class="form-control" min="0" step="1"
           value="{{ old('stock', $product->stock ?? '') }}">
    @error('stock')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- メーカー --}}
<div class="mb-3">
    <label class="form-label">メーカー名 <span class="text-danger">*</span></label>
    <select name="company_id" class="form-select">
        <option value="">メーカーを選択</option>
        @foreach($companies as $c)
            <option value="{{ $c->id }}"
                @selected(old('company_id', $product->company_id ?? '') == $c->id)>
                {{ $c->company_name }}
            </option>
        @endforeach
    </select>
    @error('company_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- コメント --}}
<div class="mb-3">
    <label class="form-label">コメント</label>
    <textarea name="comment" class="form-control" rows="3">{{ old('comment', $product->comment ?? '') }}</textarea>
    @error('comment')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- 商品画像 --}}
<div class="mb-3">
    <label class="form-label">商品画像</label>
    <input type="file" name="img" class="form-control" accept="image/*">
    @error('img')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>