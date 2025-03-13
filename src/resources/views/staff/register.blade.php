@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/register.css') }}">
@endsection

@section('content')
<h1 class="content__title">
        会員登録
</h1>
<div class="content__form">
    <form class="form__register" action="{{ route('register.store') }}" method="post">
    @csrf
        <label class="form__register--label">名前</label>
        <input class="form__register--item" type="text" name="name" value="">
        <div class="form__error">
            @error('name')
                {{ $message }}
            @enderror
        </div>
        <label class="form__register--label">メールアドレス</label>
        <input class="form__register--item" type="email" name="email" value="">
        <div class="form__error">
            @error('email')
                {{ $message }}
            @enderror
        </div>
        <label class="form__register--label">パスワード</label>
        <input class="form__register--item" type="password" name="password" value="">
        <div class="form__error">
            @error('password')
                {{ $message }}
            @enderror
        </div>
        <label class="form__register--label">パスワード確認</label>
        <input class="form__register--item" type="password" name="password_confirmation" value="" >
        <button class="form__register--button" type="submit">
            登録する
        </button>
    </form>
</div>
<div class="content__login">
    <a class="content__login--item" href="/login">ログインはこちら</a>
</div>
@endsection