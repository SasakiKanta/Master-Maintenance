@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['Users', route('users.index')]);
      array_push($bc, ['UserEdit', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

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
        <ul class="mb-1">
          <!-- エラーを出力 -->
          <li>{{ trans('messages.error.info') }}</li>
        </ul>
      </div>
      @endif 

      <!-- 入力フォーム -->
      @if (!isset($id))
      <!-- 登録 -->
      <form id="edit-form" class="form-tag" method="POST" action="{{ route('users.insert') }}">
      @else
      <!-- 更新 -->
      <form id="edit-form" class="form-tag" method="POST" action="{{ route('users.update') }}">
        <input type="hidden" id="id" name="id" value="{{$id}}">
      @endif
        @csrf
        <div>
          <label for="first_name" class="label">氏名</label>
          <input type="text" id="name" name="name" value="{{old('name', $name)}}"
            class="@error('name') error-text @enderror input-text" placeholder="">
          @error('name')
          <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">メールアドレス</label>
          <input type="text" id="email" name="email" value="{{old('email', $email)}}" 
            class="@error('email') error-text @enderror input-text" placeholder="">
          @error('email')
            <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">パスワード</label>
          <input type="password"
            class="@error('password') error-text @enderror input-text form-input w-full" name="password">
            @error('password')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
        </div>
        <div>
          <label for="last_name" class="label">アカウントロック</label>
          <input id="default-checkbox" name="isLocked" type="checkbox" value="1" class="check-box"
            @if ($isLocked)
              checked
            @endif
            >
          <label for="default-checkbox" class="check-box-label"></label>
        </div>
        <div class="flex">
          <!-- 削除ボタン -->
          <button type="button" class="delete-btn" onclick="doAction()"
            @if(!isset($id))
              hidden
            @endif
            >削除</button>
          <!-- 登録・更新ボタン -->
          <button type="submit" class="update-btn ml-auto">
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
</main>
@endsection

@push('app-script')
<script>
  // 削除ボタン押下時の処理
  function doAction() {
    if (confirm('削除します。よろしいですか？')) {
      let form = document.getElementById('edit-form');
      form.action = "{{ route('users.delete') }}";
      form.submit();
    }
  }
</script>
@endpush
