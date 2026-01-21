@extends('layouts.app')

@section('content')
<div class="container">
  <h1>商品一覧</h1>

  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- 検索フォーム（GET） --}}
  <form id="search-form"
    method="GET"
    class="row g-2 mb-3">
    {{-- 商品名キーワード --}}
    <div class="col-md-3">
      <input type="text" name="keyword"
        class="form-control"
        value="{{ $keyword }}"
        placeholder="商品名で検索">
    </div>

    {{-- メーカー --}}
    <div class="col-md-3">
      <select name="company_id" class="form-select">
        <option value="">メーカーを選択</option>
        @foreach($companies as $c)
        <option value="{{ $c->id }}" @selected($companyId==$c->id)>
          {{ $c->company_name }}
        </option>
        @endforeach
      </select>
    </div>

    {{-- 価格 下限・上限 --}}
    <div class="col-md-2">
      <input type="number" name="price_min"
        class="form-control"
        value="{{ $priceMin }}"
        placeholder="価格(下限)">
    </div>
    <div class="col-md-2">
      <input type="number" name="price_max"
        class="form-control"
        value="{{ $priceMax }}"
        placeholder="価格(上限)">
    </div>

    {{-- 検索ボタンなど --}}
    <div class="col-md-2 d-flex gap-1">
      <button class="btn btn-primary flex-fill">検索</button>
      <a href="{{ route('products.index') }}"
        class="btn btn-outline-secondary flex-fill">クリア</a>
      <a href="{{ route('products.create') }}"
        class="btn btn-success flex-fill">新規登録</a>
    </div>
    <input type="hidden" name="sort" id="sort" value="{{ $sort ?? 'id' }}">
    <input type="hidden" name="direction" id="direction" value="{{ $direction ?? 'desc' }}">
  </form>

  {{-- 検索フォームはそのまま --}}

  <div id="result-area">
    @include('products.partials.list', ['products' => $products])
  </div>

  @section('scripts')
  <script>
    console.log('products index script loaded');

    $(function() {
      function fetchList(url, data) {
        console.log('🔥 fetchList START', url, data);
        $.ajax({
            url: url,
            type: "GET",
            data: data,
            dataType: "json",
          })
          .done(function(res) {
            $("#result-area").html(res.html);
          })
          .fail(function() {
            alert("検索に失敗しました");
          });
      }

      // 検索（submit）
      $(document).off('submit', '#search-form').on('submit', '#search-form', function(e) {
        e.preventDefault();
        fetchList("{{ route('products.index') }}", $(this).serialize());
      });

      // ページネーション
      $(document).off('click', '#result-area .pagination a').on('click', '#result-area .pagination a', function(e) {
        e.preventDefault();
        fetchList($(this).attr('href'), $('#search-form').serialize());
      });

      // ソート
      $(document).off('click', '#result-area a.sortable').on('click', '#result-area a.sortable', function(e) {
        e.preventDefault();

        const clickedSort = $(this).data('sort');
        const currentSort = $('#sort').val();
        const currentDir = $('#direction').val();

        let nextDir = 'asc';
        if (clickedSort === currentSort) nextDir = (currentDir === 'asc') ? 'desc' : 'asc';

        $('#sort').val(clickedSort);
        $('#direction').val(nextDir);

        fetchList("{{ route('products.index') }}", $('#search-form').serialize());
      });

      $('#search-form').on('submit', function(e) {
        e.preventDefault();
        fetchList("{{ route('products.index') }}", $(this).serialize());
      });

      $(document).on('click', '#result-area .pagination a', function(e) {
        e.preventDefault();
        fetchList($(this).attr('href'), $('#search-form').serialize());
      });
      $(document).on('click', '.js-delete', function() {
        if (!confirm('削除してよろしいですか？')) return;

        const id = $(this).data('id');
        const $row = $(this).closest('tr');

        $.ajax({
            url: `/products/${id}`,
            type: 'POST',
            data: {
              _method: 'DELETE',
              _token: $('meta[name="csrf-token"]').attr('content')
            },
          })
          .done(function() {

            $row.fadeOut(300, function() {
              $(this).remove();
            });
          })
          .fail(function() {
            alert('削除に失敗しました');
          });
      });
    });
  </script>
  @endsection