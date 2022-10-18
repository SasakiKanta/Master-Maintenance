@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['顧客', route('users.index')]);
      array_push($bc, ['顧客編集', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
      @if ($id)
        顧客更新
      @else
        顧客登録
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

        <div class="flex">
          <div class="mr-4">
            <label for="first_name" class="label">名<span class="require-label"></span></label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label for="first_name" class="label">姓<span class="require-label"></span></label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>        
        <div class="flex">
          <div class="mr-4">
            <label for="first_name" class="label">名(フリガナ)</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label for="first_name" class="label">姓(フリガナ)</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="flex">
          <div class="mr-4">
            <label for="first_name" class="label">性別</label>
            <input id="gender-1" type="radio" value="1" name="gender" class="radio_btn">
            <label for="gender-1" class="radio_btn_label">男</label>
            <input id="gender-2" type="radio" value="2" name="gender" class="radio_btn">
            <label for="gender-2" class="radio_btn_label">女</label>
            <input id="gender-3" type="radio" value="3" name="gender" class="radio_btn">
            <label for="gender-3" class="radio_btn_label">指定しない</label>
          </div>
          <!-- 調整中 -->
          <div>
            <label for="first_name" class="label">生年月日<span class="require-label"></span></label>
            <input id="birthday" type="date" class="input_date" name="birthday" value="" maxlength="32"
              min="1900-01-01" max="9999-12-31">
          </div>
        </div>
        <div class="flex">
          <div class="mr-4">
            <label for="first_name" class="label">郵便番号</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4">
            <label for="first_name" class="label">都道府県</label>
            <select id="supplyKbn" class="select-box">
              <option selected value="">選択してください</option>
              <option value="1">北海道</option>
              <option value="47">沖縄県</option>
            </select>
          </div>
        </div>
        <div class="flex">
          <div class="mr-4 w-1/3">
            <label for="first_name" class="label">市区郡町村</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4 w-1/3">
            <label for="first_name" class="label">町名・番地</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror m-auto input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="w-1/3">
            <label for="first_name" class="label">マンション・建物名など</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror m-auto input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3 mr-4">
            <label for="last_name" class="label">電話番号</label>
            <input type="text" id="email" name="email" value="{{old('email', $email)}}" 
              class="@error('email') error-text @enderror input-text">
            @error('email')
              <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="w-2/3">
            <label for="last_name" class="label">メールアドレス<span class="require-label"></span></label>
            <input type="text" id="email" name="email" value="{{old('email', $email)}}" 
              class="@error('email') error-text @enderror input-text">
            @error('email')
              <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="flex">
          <div class="mr-4">
            <label for="last_name" class="label">取引先名</label>
            <select id="supplyKbn" class="select-box">
              <option selected value="">選択してください</option>
              <option value="1">得意先</option>
              <option value="2">仕入先</option>
            </select>
          </div>
          <div class="w-1/3">
            <label for="first_name" class="label">肩書</label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text-md">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div>
          <label for="last_name" class="label">管理側メモ</label>
          <textarea id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
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
