@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')

<h1 class="content__title">
    ログイン
</h1>
<div class="content__form">
    <form class="form__login" action="/admin/login" method="post">
        @csrf
        <label class="form__login--label">メールアドレス</label>
        <input class="form__login--item" type="email" name="email" value="{{ old('email') }}" >
        <div class="form__error">
            @error('email')
                {{ $message }}
            @enderror
        </div>
        <label class="form__login--label">パスワード</label>
        <input class="form__login--item" type="password" name="password" value="" >
        <div class="form__error">
            @error('password')
                {{ $message }}
            @enderror
        </div>
        <button class="form__login--button" type="submit">
            管理者ログインする
        </button>
    </form>
</div>
@endsection
