@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verification.css') }}">
@endsection

@section('content')
<p class="detail">
    登録していただいたメールアドレスに認証メールを送付しました。
</p>
<p class="detail">
    メール認証を完了してください。
</p>
<button type="submit" class="form__button">
    <a class="form__link" href="https://mailtrap.io/inboxes/3400198/messages" target="_blank" rel="noopener noreferrer">
        認証はこちらから
    </a>
</button>
<div class="send__link">
    <a href="{{ route('verification.send') }}">
        認証メールを再送する
    </a>
</div>

<div class="message__status">
    @if (session('message'))
        <p class="message">
            {{ session('message') }}
        </p>
    @endif
</div>
@endsection