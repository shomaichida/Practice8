@extends('layouts.app')

@section('content')
<div class="container">
  <h1>商品一覧</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- 検索フォーム（GET） --}}
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="商品名で検索">
    </div>
    <div class="col-md-4">
      <select name="company_id" class="form-select">
        <option value="">メーカーを選択</option>
        @foreach($companies as $c)
          <option value="{{ $c->id }}" @selected($companyId == $c->id)>{{ $c->company_name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary">検索</button>
      <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">クリア</a>
      <a href="{{ route('products.create') }}" class="btn btn-success">新規登録</a>
    </div>
  </form>

  @if($products->count() === 0)
    <div class="alert alert-info">該当する商品はありません。</div>
  @else
    <div class="mb-2 text-muted">
      検索結果：{{ $products->total() }}件
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>商品名</th>
          <th>価格</th>
          <th>在庫</th>
          <th>メーカー</th>
          <th>商品画像</th> {{-- ★ 追加 --}}
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->product_name }}</td>
            <td>¥{{ number_format($p->price) }}</td>
            <td>{{ $p->stock }}</td>
            <td>{{ $p->company->company_name ?? '-' }}</td>
            <td>
              @if($p->img_path)
                <img src="{{ asset('storage/' . $p->img_path) }}" alt="商品画像" width="80">
              @else
                なし
              @endif
            </td>
            <td class="d-flex gap-2">
              <a href="{{ route('products.show',$p) }}" class="btn btn-sm btn-info">詳細</a>
              <form method="POST" action="{{ route('products.destroy',$p) }}"
                    onsubmit="return confirm('削除してよろしいですか？');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{-- ページネーション（検索条件保持付き） --}}
    {{ $products->links() }}
  @endif
</div>
@endsection