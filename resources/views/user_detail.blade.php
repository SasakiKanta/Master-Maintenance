@extends('layouts.app')

@section('content')
<main class="main">
  <div class="flex">
    <div class="w-full">
      <section class="section">
        <header class="section-header">
          ユーザー登録
        </header>

        @if (session('flash_message'))
        <div class="flash flash-info ">
          <span class="ui-icon ui-icon-check"></span>
          {{ session('flash_message') }}
        </div>
        @endif

        <form class="form-tag" method="POST" action="{{ route('users.search') }}">
          @csrf

        </form>
      </section>
    </div>
  </div>
</main>
@endsection

@push('app-script')
<script>

</script>
@endpush
