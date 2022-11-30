@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['顧客', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
        顧客マスタ
      </header>

      <!-- メッセージ部 -->
      @if (session('flash_message'))
      <div class="flash flash-info ">
        <span class="ui-icon ui-icon-check"></span>
        {{ session('flash_message') }}
      </div>
      @endif

      <!-- 検索フォーム -->
      <form id="search-form" class="form-tag" method="POST" action="{{ route('customers.search') }}">
        @csrf
        <div class="search-condition">
          <div>
            <label for="name" class="label">名前</label>
            <input type="text" id="name" name="name" value="{{ $name ?? '' }}" class="input-text">
          </div>
          <div>
            <label for="name_kana" class="label">名前（カナ）</label>
            <input type="text" id="name_kana" name="name_kana" value="{{ $name_kana ?? '' }}" class="input-text">
          </div>
          <div>
            <label for="gender_type" class="label">性別</label>
            <?php foreach (\App\Enums\Gender::cases() as $case) { ?>
                <label><input type="radio" id="gender" name="gender" value="{{ $case->value }}"
                    @if(($gender ?? '') ===  $case->value) checked @endif>{{ $case->label() }}
                </label>
            <?php } ?>
          </div>
          <div>
            <label for="addr" class="label">住所</label>
            <input type="text" id="addr" name="addr" value="{{ $addr ?? '' }}" class="input-text">
          </div>
          <div>
            <label for="email" class="label">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{ $email ?? '' }}" class="input-text">
          </div>
          <div>
            <label for="supplier_name" class="label">取引先名</label>
            <input type="text" id="supplier_name" name="supplier_name" value="{{ $supplier_name ?? '' }}" class="input-text">
          </div>
        </div>

        <div class="flex">
          <button type="submit" class="search-btn">検索</button>
          <button type="button" class="clear-btn ml-4" onclick="location.href='{{ route('customers') }}';return false;">クリア</button>
          <?php if (isset($customers)) {  ?>
          <button type="button" class="search-btn ms-4" onclick="location.href='{{ route('customers.csv') }}';return false;">CSVダウンロード</button>
          <?php } ?>
          <button type="button" class="new-btn ml-auto" onclick="location.href='{{ route('customers.entry') }}';return false;">新規登録</button>
        </div>
      </form>

      <!-- 一覧表示 -->
      <?php if (isset($customers)) {  ?>
      <form id="list-form" class="form-tag" method="GET" action="{{ route('customers.paging') }}">
        <div class="flex flex-col">
          <table class="list-table">
            <!-- 一覧見出し部 -->
            <thead class="list-table-head">
              <tr class="list-table-head-tr">
                <th scope="col" class="py-2">{!! V::sortButton('id', 'ID', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('full_name', '名前', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('gender', '性別', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('addr', '住所', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('email', 'メールアドレス', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '取引先名', $sort) !!}</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <!-- 一覧部 -->
            <tbody class="list-table-body">
            <?php foreach ($customers as $customer) { ?>
              <tr class="list-table-body-tr">
                <td class="py-2 px-4">{{ $customer->id }}</td>
                <td class="py-2 px-4">{{ $customer->full_name }}</td>
                <td class="py-2 px-4">{{ $customer->genderLabel }}</td>
                <td class="py-2 px-4">{{ $customer->addr }}</td>
                <td class="py-2 px-4">{{ $customer->email }}</td>
                <td class="py-2 px-4">{{ $customer->name }}</td>
                <td class="text-center">
                  <button type="button" class="edit-btn" onclick='location.href="/customers/{{$customer->id}}";return false;'>更新</button>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
          @if (count($customers) == 0)
              <div class="mt-10 text-center">対象データなし</div>
          @endif
          <!-- ページング -->
          <div class="mt-10">
            {{ $customers->links() }}
          </div>
        </div>
      </form>
      <?php }  ?>
    </section>
  </div>
</main>
@endsection
