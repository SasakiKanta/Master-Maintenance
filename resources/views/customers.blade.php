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
      <form id="search-form" class="form-tag" method="POST" action="{{ route('users.search') }}">
        @csrf
        <div class="search-condition">
          <div>
            <label for="last_name" class="label">名前</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="first_name" class="label">名前(カナ)</label>
            <input type="text" id="name" name="name" value="{{$name}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="first_name" class="label">性別</label>
            <input id="gender-1" type="radio" value="1" name="gender" class="radio_btn">
            <label for="gender-1" class="radio_btn_label">男</label>
            <input id="gender-2" type="radio" value="2" name="gender" class="radio_btn">
            <label for="gender-2" class="radio_btn_label">女</label>
            <input id="gender-3" type="radio" value="3" name="gender" class="radio_btn">
            <label for="gender-3" class="radio_btn_label">指定しない</label>
          </div>
          <div>
            <label for="last_name" class="label">住所</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="last_name" class="label">電話番号</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="last_name" class="label">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="last_name" class="label">取引先名</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
        </div>

        <div class="flex">
          <button type="submit"class="search-btn" >検索</button>
          <button type="button" class="clear-btn ml-4" onclick="location.href='{{ route('users.clear') }}';return false;">クリア</button>
          <button type="button" class="new-btn ml-auto" onclick="location.href='{{ route('users.entry') }}';return false;">新規登録</button>
        </div>
      </form>
      <!-- 一覧表示 -->
      <?php if (isset($users)) {  ?>
      <form id="list-form" class="form-tag" method="GET" action="{{ route('users.paging') }}">
        <div class="flex flex-col">
          <table class="list-table">
            <!-- 一覧見出し部 -->
            <thead class="list-table-head">
              <tr class="list-table-head-tr">
                <th scope="col" class="py-2">{!! V::sortButton('id', 'ID', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '名前', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '性別', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '住所', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('email', 'メールアドレス', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('email', '取引先名', $sort) !!}</th>
                <th scope="col" class="py-2">アカウントロック</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <!-- 一覧部 -->
            <tbody class="list-table-body">
            <?php foreach ($users as $user) { ?>
              <tr class="list-table-body-tr">
                <td class="py-2 px-4 text-center">{{$user->id}}</td>
                <td class="py-2 px-4"><a href="/users/{{$user->id}}">{{$user->name}}</a></td>
                <td class="py-2 px-4">男</td>
                <td class="py-2 px-4">沖縄県沖縄市住所ABCDEFGH</td>
                <td class="py-2 px-4">{{ $user->email}}</td>
                <td class="py-2 px-4">取引先A</td>
                <td class="py-2 px-4 text-center">
                  @if ($user->is_locked)
                  <span class="badge-yellow">ロック</span>
                  @else
                  <span class="badge-green">有効</span>
                  @endif
                </td>
                <td class="text-center">
                  <button type="button" class="edit-btn" onclick='location.href="/users/{{$user->id}}";return false;'>更新</button>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
          @if (count($users) == 0)
              <div class="mt-10 text-center">対象データなし</div>
          @endif
          <!-- ページング -->
          <div class="mt-10">
            {{ $users->links() }}
          </div>
        </div>
      </form>
      <?php }  ?>
    </section>
  </div>
</main>
@endsection
