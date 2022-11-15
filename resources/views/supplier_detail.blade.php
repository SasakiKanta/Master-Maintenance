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
            class="@error('code') error-text @enderror input-text" @if($id) readonly @endif >
          @error('code')
          <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先名<span class="require-label"></span></label>
          <input type="text" id="name" name="name" value="{{old('name', $name)}}" 
            class="@error('name') error-text @enderror input-text">
          @error('name')
            <p class="valid-msg">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="last_name" class="label">取引先区分<span class="require-label"></span></label>
          <select id="supplyKbn" name='supplier_type' class="w-max pr-8 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected value="">選択してください</option>
            <?php foreach (\App\Enums\SupplierType::cases() as $case) { ?>
              <option value="{{ $case->value }}" @if(old('supplier_type', $supplier_type) === $case->value) selected @endif>{{ $case->label() }}</option>
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
