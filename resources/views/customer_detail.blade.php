@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['顧客', route('customers.index')]);
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
      <form id="edit-form" class="form-tag" method="POST" action="{{ route('customers.update', $id) }}">
        @csrf
        <input type="hidden" id="form-method" name="_method" value="{{ $id? 'POST': 'PUT'; }}">

        <div class="flex">
          <div>
            <label for="surname" class="label">姓<span class="require-label"></span></label>
            <input type="text" id="surname" name="surname" value="{{old('surname', $surname)}}"
              class="@error('surname') error-text @enderror input-text" placeholder="">
            @error('surname')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4">
            <label for="name" class="label">名<span class="require-label"></span></label>
            <input type="text" id="name" name="name" value="{{old('name', $name)}}"
              class="@error('name') error-text @enderror input-text" placeholder="">
            @error('name')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>        
        <div class="flex">
          <div>
            <label for="surname_kana" class="label">姓(フリガナ)</label>
            <input type="text" id="surname_kana" name="surname_kana" value="{{old('surname_kana', $surname_kana)}}"
              class="@error('surname_kana') error-text @enderror input-text" placeholder="">
            @error('surname_kana')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4">
            <label for="name_kana" class="label">名(フリガナ)</label>
            <input type="text" id="name_kana" name="name_kana" value="{{old('name_kana', $name_kana)}}"
              class="@error('name_kana') error-text @enderror input-text" placeholder="">
            @error('name_kana')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="flex">
          <div class="mr-4">
            <label for="gender" class="label">性別</label>
            <?php foreach (\App\Enums\Gender::cases() as $case) { ?>
              <input id={{'gender-' . $case->value}} type="radio" name="gender" class="radio_btn" value="{{ $case->value }}" @if(($gender ?? '') === $case->value) checked @endif></input>
              <label for={{'gender-' . $case->value}} class="radio_btn_label">{{ $case->label() }}</label>
            <?php } ?>
            @error('gender')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label for="birthday" class="label">生年月日<span class="require-label"></span></label>
            <input id="birthday" type="date" class="input_date" name="birthday" value="{{old('birthday', $birthday)}}" maxlength="32"
              min="1900-01-01" max="9999-12-31">
          </div>
        </div>
        <div class="flex">
          <div class="mr-4">
            <label for="zip" class="label">郵便番号</label>
            <input type="text" id="zip" name="zip" value="{{old('zip', $zip)}}"
              class="@error('zip') error-text @enderror input-text" placeholder="">
            @error('zip')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4">
            <label for="prefcode" class="label">都道府県</label>
            <select id="prefcode" class="prefcode-box" name="prefcode">
              <option selected value="">選択してください</option>
              <?php foreach (\App\Enums\Pref::cases() as $case) { ?>
                <option value="{{ $case->value }}" @if(($prefcode ?? '') === $case->value) selected @endif>{{ $case->label() }}</option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="flex">
          <div class="mr-4 w-1/3">
            <label for="addr_1" class="label">市区郡町村</label>
            <input type="text" id="addr_1" name="addr_1" value="{{old('addr_1', $addr_1)}}"
              class="@error('addr_1') error-text @enderror input-text" placeholder="">
            @error('addr_1')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="mr-4 w-1/3">
            <label for="addr_2" class="label">町名・番地</label>
            <input type="text" id="addr_2" name="addr_2" value="{{old('addr_2', $addr_2)}}"
              class="@error('addr_2') error-text @enderror m-auto input-text" placeholder="">
            @error('addr_2')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="w-1/3">
            <label for="addr_3" class="label">マンション・建物名など</label>
            <input type="text" id="addr_3" name="addr_3" value="{{old('addr_3', $addr_3)}}"
              class="@error('addr_3') error-text @enderror m-auto input-text" placeholder="">
            @error('addr_3')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3 mr-4">
            <label for="tel" class="label">電話番号</label>
            <input type="text" id="tel" name="tel" value="{{old('tel', $tel)}}" 
              class="input-text">
          </div>
          <div class="w-2/3">
            <label for="email" class="label">メールアドレス<span class="require-label"></span></label>
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
            <select id="supplyKbn" class="select-box" name="supplier_id">
              <option selected value="">選択してください</option>
              <?php foreach ($suppliers as $supplier) { ?>
                <option value="{{ $supplier->id }}" @if(($supplier_id ?? '') === (string)$supplier->id) selected @endif>{{ $supplier->name }}</option>
              <?php } ?>
            </select>
            @error('supplier_id')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
          <div class="w-1/3">
            <label for="position" class="label">肩書</label>
            <input type="text" id="position" name="position" value="{{old('position', $position)}}"
              class="@error('position') error-text @enderror input-text-md">
            @error('position')
            <p class="valid-msg">{{ $message }}</p>
            @enderror
          </div>
        </div>
        <div>
          <label for="remark" class="label">管理側メモ</label>
          <textarea id="message" rows="4" name="remark" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{old('remark', $remark)}}</textarea>
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
      form.action = "{{ route('customers.delete', $id) }}";
      document.getElementById('form-method').value = 'DELETE';
      form.submit();
    }
  }
</script>
@endpush
