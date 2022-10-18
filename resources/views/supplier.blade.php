@extends('layouts.app')

@section('content')
<main class="main">
  <div class="w-full sm:px-6">
    <!-- パンくず -->
    <?php
      $bc = array();
      array_push($bc, ['得意先', '']);
    ?>
    {!! BreadcrumbHelper::tag($bc) !!}

    <section class="section">
      <header class="section-header">
        得意先マスタ
      </header>

      <!-- メッセージ部 -->
      @if (session('flash_message'))
      <div class="flash flash-info ">
        <span class="ui-icon ui-icon-check"></span>
        {{ session('flash_message') }}
      </div>
      @endif

      <!-- 検索フォーム -->
      <form id="search-form" class="form-tag" method="POST" action="{{ route('users.search') }}">
        @csrf
        <div class="search-condition">
          <div>
            <label for="first_name" class="label">取引先コード</label>
            <input type="text" id="name" name="name" value="{{$name}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="second_name" class="label">取引先名</label>
            <input type="text" id="email" name="email" value="{{$email}}" class="input-text" placeholder="">
          </div>
          <div>
            <label for="supplyKbn" class="label">取引先区分</label>
            <select id="supplyKbn" class="w-max pr-8 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
              <option selected value="">すべて</option>
              <option value="1">得意先</option>
              <option value="2">仕入先</option>
            </select>
          </div>
        </div>

        <div class="flex">
          <button type="submit"class="search-btn" >検索</button>
          <button type="button" class="clear-btn ml-4" onclick="location.href='{{ route('users.clear') }}';return false;">クリア</button>
          <button type="button" class="new-btn ml-auto" onclick="location.href='{{ route('users.entry') }}';return false;">新規登録</button>
        </div>
      </form>
      <!-- 一覧表示 -->
      <?php if (isset($users)) {  ?>
      <form id="list-form" class="form-tag" method="GET" action="{{ route('users.paging') }}">
        <div class="flex flex-col">
          <table class="list-table">
            <!-- 一覧見出し部 -->
            <thead class="list-table-head">
              <tr class="list-table-head-tr">
                <th scope="col" class="py-2">{!! V::sortButton('id', 'ID', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('name', '取引先コード', $sort) !!}</th>
                <th scope="col" class="py-2">{!! V::sortButton('email', '取引先名', $sort) !!}</th>
                <th scope="col" class="py-2">取引先区分</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <!-- 一覧部 -->
            <tbody class="list-table-body">
            <?php 
            $i = 1;
            foreach ($users as $user) { ?>
              <tr class="list-table-body-tr">
                <td class="py-2 px-4 text-center">{{$user->id}}</td>
                <td class="py-2 px-4"><a href="/users/{{$user->id}}">code-{{$i}}</a></td>
                <td class="py-2 px-4">取引先名{{$i}}</td>
                <td class="py-2 px-4 text-center">
                  @if ($user->is_locked)
                  <span>仕入先</span>
                  @else
                  <span>得意先</span>
                  @endif
                </td>
                <td class="text-center">
                  <button type="button" class="edit-btn" onclick='location.href="/users/{{$user->id}}";return false;'>更新</button>
                </td>
              </tr>
            <?php
            $i++;
            } ?>
            </tbody>
          </table>
          @if (count($users) == 0)
              <div class="mt-10 text-center">対象データなし</div>
          @endif
          <!-- ページング -->
          <div class="mt-10">
            {{ $users->links() }}
          </div>
        </div>
      </form>
      <?php }  ?>
    </section>
  </div>
</main>
@endsection
