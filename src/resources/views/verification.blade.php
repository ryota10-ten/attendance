<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachTech</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/verification.css')}}">
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
    </main>
</body>
</html>