@extends('layouts.app')

@section('content')
<main class="main">
  <div class="flex">
    <div class="w-full">
      <section class="section">
        <header class="section-header">
          ユーザーマスタ
        </header>

        <!-- メッセージ部 -->
        @if (session('flash_message'))
        <div class="flash flash-info ">
          <span class="ui-icon ui-icon-check"></span>
          {{ session('flash_message') }}
        </div>
        @endif

        <form class="form-tag" method="POST" action="{{ route('users.search') }}">
          @csrf

          <!-- 検索フォーム -->
          <div class="search-condition">
            <div>
                <label for="first_name" class="label">氏名</label>
                <input type="text" id="name" name="name" value="{{$name}}" class="input-text" placeholder="">
            </div>
            <div>
                <label for="last_name" class="label">メールアドレス</label>
                <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
            </div>
          </div>

          <div class="flex">
            <button class="search-btn" type="submit">検索</button>
            <button type="button" class="new-btn ml-auto" onclick="location.href='{{ route('users.entry') }}';return false;">新規登録</button>
          </div>

          <!-- 一覧表示 -->
          <?php if (isset($users)) {  ?>
          <div class="flex flex-col">
            <table class="list-table">
              <!-- 一覧見出し部 -->
              <thead class="list-table-head">
                <tr class="list-table-head-tr">
                  <th scope="col" class="py-2">
                    <button name="st1"
                      class="sort_button @if($st1=='up') sort_button_up @elseif($st1=='down') sort_button_down @endif"
                      value=@if($st1=='up') "down" @else "up" @endif>ＩＤ</button>
                  </th>
                  <th scope="col" class="py-2">
                    <button name="st2"
                      class="sort_button @if($st2=='up') sort_button_up @elseif($st2=='down') sort_button_down @endif"
                      value=@if($st2=='up') "down" @else "up" @endif>氏名</button>
                  </th>
                  <th scope="col" class="py-2">
                    <button name="st3"
                      class="sort_button @if($st3=='up') sort_button_up @elseif($st3=='down') sort_button_down @endif"
                      value=@if($st3=='up') "down" @else "up" @endif>メールアドレス</button>
                  </th>
                </tr>
              </thead>
              <!-- 一覧部 -->
              <tbody class="list-table-body">
                <?php foreach ($users as $user) { ?>
                  <tr class="list-table-body-tr">
                    <td class="py-2 px-4">{{$user->id}}</td>
                    <td class="py-2 px-4"><a href="/users/{{$user->id}}">{{$user->name}}</a></td>
                    <td class="py-2 px-4">{{ $user->email}}</td>
                  </tr>
                <?php }  ?>
              </tbody>
            </table>
            <!-- ページング -->
            <div class="my-10">
              {{ $users->appends($pagenateParams)->links() }}
            </div>
          </div>
          <?php }  ?>
        </form>
      </section>
    </div>
  </div>
</main>
@endsection

@push('app-script')
<script>
    // 新規登録押下時の処理
    function doAction(actionNo) {
      var formInfo = document.forms[1];
      var action = new String();
      var no = new Number();
      no = parseInt(actionNo);

      action = "";

      switch (no) {
        // 新規登録ボタン押下時
        case 1:
          action = "/UserDetail/create";
          break;
      }

      if (action != "") {
        formInfo.action = action;
        submit(formInfo);
      }
    }
</script>
@endpush
