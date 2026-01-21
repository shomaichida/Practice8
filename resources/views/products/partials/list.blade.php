{{-- resources/views/products/partials/list.blade.php --}}

@php
$currentSort = request('sort', 'id');
$currentDir = request('direction', 'desc');

$nextDir = function ($col) use ($currentSort, $currentDir) {
return $currentSort === $col
? ($currentDir === 'asc' ? 'desc' : 'asc')
: 'desc';
};

$sortLink = function ($col) use ($nextDir) {
return route('products.index', array_merge(request()->query(), [
'sort' => $col,
'direction' => $nextDir($col),
]));
};
@endphp

@if($products->count() === 0)
<div class="alert alert-info">該当する商品はありません。</div>
@else
<div class="mb-2 text-muted">
  検索結果：{{ $products->total() }}件
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th><a href="#" class="sortable" data-sort="id">ID</a></th>
      <th><a href="#" class="sortable" data-sort="product_name">商品名</a></th>
      <th><a href="#" class="sortable" data-sort="price">価格</a></th>
      <th><a href="#" class="sortable" data-sort="stock">在庫</a></th>
      <th><a href="#" class="sortable" data-sort="company_id">メーカー</a></th>
      <th>商品画像</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $p)
    <tr>
      <td>{{ $p->id }}</td>
      <td>{{ $p->product_name }}</td>
      <td>{{ number_format($p->price) }}</td>
      <td>{{ $p->stock }}</td>
      <td>{{ $p->company->company_name ?? '-' }}</td>
      <td>
        @if($p->img_path)
        <img src="{{ asset('storage/'.$p->img_path) }}" style="height:60px;">
        @else
        なし
        @endif
      </td>
      <td class="d-flex gap-2">
        <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-info">詳細</a>
        <form method="POST"
          action="{{ route('products.destroy', $p) }}"
          class="d-inline js-delete-form">
          @csrf
          @method('DELETE')
        </form>
        <button type="button"
          class="btn btn-sm btn-danger js-delete"
          data-id="{{ $p->id }}">
          削除
        </button>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center">
  {{ $products->appends(request()->query())->links() }}
</div>
@endif

<style>
  th.sortable {
    cursor: pointer;
  }
</style>