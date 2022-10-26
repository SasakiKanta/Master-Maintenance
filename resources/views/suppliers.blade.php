@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['得意先', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
        取引先マスタ
      </header>

      <!-- メッセージ部 -->
      @if (session('flash_message'))
      <div class="flash flash-info ">
        <span class="ui-icon ui-icon-check"></span>
        {{ session('flash_message') }}
      </div>
      @endif

      <!-- 検索フォーム -->
      <form id="search-form" class="form-tag" method="POST" action="{{ route('suppliers.search') }}">
        @csrf
        <div class="search-condition">
          <div>
            <label for="first_name" class="label">取引先コード</label>
            <input type="text" id="code" name="code" value="{{$code ?? '' }}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="second_name" class="label">取引先名</label>
            <input type="text" id="name" name="name" value="{{$name ?? '' }}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="supplyKbn" class="label">取引先区分</label>
            <select id="supplyKbn" name='supplier_type' class="select-box">
              <option selected value="">すべて</option>
              <?php foreach (\App\Enums\SupplierType::cases() as $case) { ?>
                <option value="{{ $case->value }}" @if(($supplier_type ?? '') === $case->value) selected @endif>{{ $case->label() }}</option>
              <?php } ?>
            </select>
          </div>
        </div>

        <div class="flex">
          <button type="submit"class="search-btn" >検索</button>
          <button type="button" class="clear-btn ml-4" onclick="location.href='{{ route('suppliers') }}';return false;">クリア</button>
          <button type="button" class="new-btn ml-auto" onclick="location.href='{{ route('suppliers.entry') }}';return false;">新規登録</button>
        </div>
      </form>
      <!-- 一覧表示 -->
      <?php if (isset($suppliers)) {  ?>
      <form id="list-form" class="form-tag" method="GET" action="{{ route('suppliers.paging') }}">
        <div class="flex flex-col">
          <table class="list-table">
            <!-- 一覧見出し部 -->
            <thead class="list-table-head">
              <tr class="list-table-head-tr">
                <th scope="col" class="py-2">{!! V::sortButton('id', 'ID', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('code', '取引先コード', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '取引先名', $sort) !!}</th>
                <th scope="col" class="py-2">取引先区分</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <!-- 一覧部 -->
            <tbody class="list-table-body">
            <?php 
            $i = 1;
            foreach ($suppliers as $supplier) { ?>
              <tr class="list-table-body-tr">
                <td class="py-2 px-4 text-center">{{$supplier->id}}</td>
                <td class="py-2 px-4"><a href="/suppliers/{{$supplier->id}}">{{$supplier->code}}</a></td>
                <td class="py-2 px-4">{{$supplier->name}}</td>
                <td class="py-2 px-4 text-center">{{$supplier->supplierTypeLabel}}</td>
                <td class="text-center">
                  <button type="button" class="edit-btn" onclick='location.href="/suppliers/{{$supplier->id}}";return false;'>更新</button>
                </td>
              </tr>
            <?php
            $i++;
            } ?>
            </tbody>
          </table>
          @if (count($suppliers) == 0)
              <div class="mt-10 text-center">対象データなし</div>
          @endif
          <!-- ページング -->
          <div class="mt-10">
            {{ $suppliers->links() }}
          </div>
        </div>
      </form>
      <?php }  ?>
    </section>
  </div>
</main>
@endsection
