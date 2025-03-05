<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachTech</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
    <title>CoachTech</title>
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo--img" src="{{asset('img/logo.svg')}}" alt="CoachTech">
        </div>
    </header>
    <main class="content">
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
                <button class="form__login--button" type="submit">管理者ログインする</button>
            </form>
        </div>
    </main>
</body>
</html>