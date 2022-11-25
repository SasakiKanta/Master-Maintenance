@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['取引先', route('suppliers.index')]);
      array_push($bc, ['取引先編集', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
      @if ($id)
        取引先更新
      @else
        取引先登録
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
      <form id="edit-form" class="form-tag" method="POST" action="{{ route('suppliers.update', $id) }}">
        @csrf
        <input type="hidden" id="form-method" name="_method" value="{{ $id? 'POST': 'PUT'; }}">

        <div>
          <label for="first_name" class="label">取引先コード<span class="require-label"></span></label>
          <input type="text" id="code" name="code" value="{{old('code', $code)}}"
            class="@error('code') error-text @enderror input-text" placeholder="" @if($id) readonly @endif>
          @error('code')
          <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先名<span class="require-label"></span></label>
          <input type="text" id="name" name="name" value="{{old('name', $name)}}"
            class="@error('name') error-text @enderror input-text" placeholder="">
          @error('name')
            <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先区分<span class="require-label"></span></label>
          <select id="supplier_type" name='supplier_type' class="select-box @error('supplier_type') error-text @enderror input-text">
            <option value="">選択してください</option>
            <?php foreach (\App\Enums\SupplierType::cases() as $case) { ?>
              <option value="{{ $case->value }}"
                @if( old('supplier_type', $supplier_type) === $case->value) selected @endif>{{ $case->label() }}</option>
            <?php } ?>
          </select>
            @error('supplier_type')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="last_name" class="label">担当者<span style="color: rgb(30 64 175); font-size: 0.75rem; line-height: 1rem; padding-left: 0.625rem; padding-right: 0.625rem; padding-top: 0.125rem; padding-bottom: 0.125rem; border-radius: 0.25rem; margin-left: 1.25rem; background-color: rgb(191 219 254);">任意</span></label>
            <select id="user_id" name='user_id' class="select-box">
              <option value="">選択してください</option>
              <?php foreach ($users as $user) { ?>
                <option value="{{$user->id}}"
                @if( old('user_id', $user_id) == $user->id) selected @endif>
                {{$user->name}}</option>
              <?php } ?>
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
      form.action = "{{ route('suppliers.delete', $id) }}";
      document.getElementById('form-method').value = 'DELETE';
      form.submit();
    }
  }
</script>
@endpush
