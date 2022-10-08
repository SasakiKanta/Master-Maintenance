@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['ユーザー', route('users.index')]);
      array_push($bc, ['ユーザー編集', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
      @if ($id)
        ユーザー更新
      @else
        ユーザー登録
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
      <form id="edit-form" class="form-tag" method="POST" action="{{ route('users.update', $id) }}">
        @csrf
        <input type="hidden" id="form-method" name="_method" value="{{ $id? 'POST': 'PUT'; }}">

        <div>
          <label for="first_name" class="label">名前<span class="require-label"></span></label>
          <input type="text" id="name" name="name" value="{{old('name', $name)}}"
            class="@error('name') error-text @enderror input-text" placeholder="">
          @error('name')
          <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">メールアドレス<span class="require-label"></span></label>
          <input type="text" id="email" name="email" value="{{old('email', $email)}}" 
            class="@error('email') error-text @enderror input-text" placeholder="">
          @error('email')
            <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">パスワード@if(!$id)<span class="require-label"></span>@endif</label>
          <input type="password"
            class="@error('password') error-text @enderror input-text form-input w-full" name="password">
            @error('password')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-start mb-6">
          <div class="flex items-center h-5">
            <input type="checkbox" id="is-locked" name="isLocked" value="1" class="check-box"
            @if ($isLocked)
              checked
            @endif
            >
          </div>
          <label for="is-locked" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">アカウントロック</label>
        </div>
        <div class="flex">
          <!-- 登録・更新ボタン -->
          <button type="submit" class="update-btn">
            @if($id)
              更新
            @else
              登録
            @endif
          </button>
          <!-- 削除ボタン -->
          <button type="button" class="delete-btn ml-auto" onclick="doAction()" @if(!$id) hidden @endif>削除</button>
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
      form.action = "{{ route('users.delete', $id) }}";
      document.getElementById('form-method').value = 'DELETE';
      form.submit();
    }
  }
</script>
@endpush
