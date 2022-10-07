@extends('layouts.app')

@section('content')
<main class="main">
  <div class="flex">
    <div class="w-full">
      <section class="section">
        <header class="section-header">
        @if (!isset($id))
        ユーザー登録
        @else
        ユーザー更新
        @endif
        </header>

        <!-- メッセージ部 -->
        @if (session('flash_message'))
        <div class="flash-info">
          {{ session('flash_message') }}
        </div>
        @endif

        <!-- エラーメッセージ部 -->
        @if ($errors->any())
        <div class="flash-err">
          <ul class="mb-2">
            <!-- 全てのエラーを出力 -->
            @foreach ($errors->all() as $error)
            <li class="mb-1">{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif 

        <!-- 入力フォーム -->
        @if (!isset($id))
        <!-- 登録 -->
        <form class="form-tag" method="POST" action="{{ route('users.insert') }}">
        @else
        <!-- 更新 -->
        <form class="form-tag" method="POST" action="{{ route('users.update') }}">
          <input type="hidden" id="id" name="id" value="{{$id}}">
        @endif
          @csrf
          <div>
            <label for="first_name" class="label">氏名</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="last_name" class="label">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{old('email', $email)}}" class="input-text" placeholder="" required>
          </div>
          <div>
            <label for="last_name" class="label">パスワード</label>
            <input type="password"
              class="input-text form-input w-full @error('password') border-red-500 @enderror" name="password">
          </div>
          <div>
            <label for="last_name" class="label">アカウントロック</label>
            <input type="radio" name="lock" value="true">
            <input type="radio" name="lock" value="false">
          </div>
          <div class="flex">
            <!-- 削除ボタン -->
            <button type="button" class="new-btn" onclick="doAction()"
              @if(!isset($id))
                hidden
              @endif
              >削除</button>
            <!-- 登録・更新ボタン -->
            <button type="submit" class="new-btn ml-auto">
              @if(!isset($id))
                登録
              @else
                更新
              @endif
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
</main>
@endsection

@push('app-script')
<script>
  // 削除ボタン押下時の処理
  function doAction() {
    // フォームの取得
    var formInfo = document.forms[1];
    var action = new String();
    var no = new Number();

    action = "{{ route('users.delete') }}";

    if (action != "") {
      formInfo.action = action;
      formInfo.submit();
    }
  }
</script>
@endpush
