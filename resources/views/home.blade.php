@extends('layouts.app')

@section('content')
<main class="main">
    <div class="w-full sm:px-6">

        @if (session('status'))
            <div class="text-sm border border-t-8 rounded text-green-700 border-green-600 bg-green-100 px-3 py-4 mb-4" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <section class="section">

            <header class="section-header">
                Home
            </header>

            <div class="w-full p-6">
                <p class="text-gray-700">
                    Hello!

                </p>
            </div>
        </section>
    </div>
</main>
@endsection
