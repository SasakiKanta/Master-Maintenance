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
        <ul class="mb-1" style="padding: 0px">
          <!-- エラーを出力 -->
          <li>{{ trans('messages.error.info') }}</li>
        </ul>
      </div>
      @endif

      <!-- 入力フォーム -->
        <form id="edit-form" class="form-tag" method="POST" action="{{ route('customers.update', $id) }}">
            @csrf
            <input type="hidden" id="form-method" name="_method" value="{{ $id? 'POST': 'PUT'; }}">
            <div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col">
                    <label for="surname" class="label">姓<span class="require-label"></span></label>
                    <input type="text" id="surname" name="surname" value="{{old('surname', $surname)}}"
                        class="@error('surname') error-text @enderror input-text" placeholder="">
                    @error('surname')
                    <p class="valid-msg">{{ $message }}</p>
                    @enderror
                    </div>
                    <div class="me-3 col">
                    <label for="name" class="label">名<span class="require-label"></span></label>
                    <input type="text" id="name" name="name" value="{{old('name', $name)}}"
                        class="@error('name') error-text @enderror input-text" placeholder="">
                    @error('name')
                        <p class="valid-msg">{{ $message }}</p>
                    @enderror
                    </div>
                    <div class="text-center col">
                    <label class="label">顧客区分</label>
                    <?php foreach (\App\Enums\CustomerType::cases() as $case) { ?>
                        <label>
                            <input type="radio" name="customer_type" id="customer{{ $case->value }}" value="{{ $case->value }}" onclick="customerTypeChange();"
                                @if( old('customer_type', $customer_type) === $case->value) checked
                                @elseif($case->value == 1 && !$id) checked
                                @endif
                                >{{ $case->label() }}
                        </label>
                    <?php } ?>
                    </div>
                </div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col-4">
                        <label for="surname_kana" class="label">姓（フリガナ）</label>
                        <input type="text" id="surname_kana" name="surname_kana" value="{{old('surname_kana', $surname_kana)}}"
                        class="@error('surname_kana') error-text @enderror input-text" placeholder="">
                        @error('surname_kana')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-4">
                        <label for="surname_kana" class="label">名（フリガナ）</label>
                        <input type="text" id="name_kana" name="name_kana" value="{{old('name_kana', $name_kana)}}"
                        class="@error('name_kana') error-text @enderror input-text" placeholder="">
                        @error('name_kana')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col-4">
                        <label for="gender_type" class="label">性別</label>
                        <?php foreach (\App\Enums\Gender::cases() as $case) { ?>
                            <label><input type="radio" id="gender" name="gender" value="{{ $case->value }}"
                                @if( old('gender', $gender) === $case->value) checked
                                @elseif($case->value == 1 && !$id) checked
                                @endif>{{ $case->label() }}
                            </label>
                        <?php } ?>
                        <label><input type="radio" id="gender" name="gender" value="4">チェック</label>
                        @error('gender')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-4">
                        <label for="birthyday" class="label">生年月日<span class="require-label"></span></label>
                        <input type="date" id="birthday" name="birthday" value="{{old('birthday', $birthday)}}"
                        class="@error('birthday') error-text @enderror input-text" placeholder="">
                        @error('birthday')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col-4">
                        <label for="zip" class="label">郵便番号</label>
                        <input type="text" id="zip" name="zip" value="{{old('zip', $zip)}}"
                        class="@error('zip') error-text @enderror input-text" placeholder="">
                        @error('zip')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-4">
                        <label for="prefcode" class="label">都道府県</label>
                        <select id="prefcode" name='prefcode' class="input-text @error('prefcode') error-text @enderror input-text">
                        <option value="">選択してください</option>
                        <?php foreach (\App\Enums\Pref::cases() as $case) { ?>
                            <option value="{{ $case->value }}"
                            @if( old('prefcode', $prefcode) === $case->value) selected @endif>{{ $case->label() }}</option>
                        <?php } ?>
                        </select>
                        @error('prefcode')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col">
                        <label for="addr_1" class="label">市区群町村</label>
                        <input type="text" id="addr_1" name="addr_1" value="{{old('addr_1', $addr_1)}}"
                        class="@error('addr_1') error-text @enderror input-text" placeholder="">
                        @error('addr_1')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="me-3 col">
                        <label for="addr_2" class="label">町名・番地</label>
                        <input type="text" id="addr_2" name="addr_2" value="{{old('addr_2', $addr_2)}}"
                        class="@error('addr_2') error-text @enderror input-text-md" placeholder="">
                        @error('addr_2')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="addr_3" class="label">マンション・建物名など</label>
                        <input type="text" id="addr_3" name="addr_3" value="{{old('addr_3', $addr_3)}}"
                        class="@error('addr_3') error-text @enderror input-text" placeholder="">
                        @error('addr_3')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-start mb-4">
                    <div class="me-3 col-4">
                        <label for="tel" class="label">電話番号</label>
                        <input type="text" id="tel" name="tel" value="{{old('tel', $tel)}}"
                        class="@error('tel') error-text @enderror input-text" placeholder="">
                        @error('tel')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-8">
                        <label for="email" class="label">メールアドレス<span class="require-label"></span></label>
                        <input type="text" id="email" name="email" value="{{old('email', $email)}}"
                        class="@error('email') error-text @enderror input-text" placeholder="">
                        @error('email')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <div class="me-3 col mb-4" id="supplier_name"  style="@if(!(old('customer_type', $customer_type) == 2)) display: none @endif">
                        <label for="supplier_id" class="label">取引先名<span class="require-label"></span></label>
                        <select id="supplier_id" name='supplier_id' class="@error('supplier_id') error-text @enderror input-text">
                        <option value="">選択してください</option>
                        <?php foreach ($suppliers as $supplier) { ?>
                            <option value="{{$supplier->id}}"
                            @if( old('supplier_id', $supplier_id) == $supplier->id) selected @endif>
                            {{$supplier->name}}</option>
                        <?php } ?>
                        <option value="1111111111111111111">チェック</option>
                        </select>
                        @error('supplier_id')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col mb-4" id="title"  style="@if(!(old('customer_type', $customer_type) == 2)) display: none @endif">
                        <label for="position" class="label">肩書</label>
                        <input type="text" id="position" name="position" value="{{old('position', $position)}}"
                        class="@error('position') error-text @enderror input-text" placeholder="">
                        @error('position')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                    <div class="mb-4">
                        <label for="remark" class="label">管理者メモ</label>
                        <textarea id="remark" name="remark"
                        class="@error('remark') error-text @enderror input-text" placeholder="">{{old('remark', $remark)}}</textarea>
                        @error('remark')
                        <p class="valid-msg">{{ $message }}</p>
                        @enderror
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
  //顧客区分のボタンが押されたときの処理
  function customerTypeChange() {

    radio = document.querySelectorAll(`input[type='radio'][name='customer_type']`);

    if(radio[0].checked) {
        document.getElementById('supplier_name').style.display =  "none";
        document.getElementById('title').style.display = "none";
        document.getElementById('supplier_id').selectedIndex = 0;
        document.getElementById('position').value = "";
    }else if(radio[1].checked) {
        document.getElementById('supplier_name').style.display =  "";
        document.getElementById('title').style.display = "";
    };
  }
</script>
@endpush
