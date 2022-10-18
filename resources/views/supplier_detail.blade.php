@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['得意先', route('users.index')]);
      array_push($bc, ['得意先編集', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
      @if ($id)
        得意先更新
      @else
        得意先登録
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
          <label for="first_name" class="label">取引先コード<span class="require-label"></span></label>
          <input type="text" id="name" name="name" value="{{old('name', $name)}}"
            class="@error('name') error-text @enderror input-text" placeholder="">
          @error('name')
          <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先名<span class="require-label"></span></label>
          <input type="text" id="email" name="email" value="{{old('email', $email)}}" 
            class="@error('email') error-text @enderror input-text" placeholder="">
          @error('email')
            <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先区分<span class="require-label"></span></label>
          <select id="supplyKbn" class="w-max pr-8 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected value="">選択してください</option>
            <option value="1">得意先</option>
            <option value="2">仕入先</option>
          </select>
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
