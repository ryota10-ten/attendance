<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachTech</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/header.css')}}">
    @yield('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
</head>
<body class="{{ (Auth::guard('admin')->check() || Auth::guard('web')->check()) ? 'content__page' : '' }}">
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img class="header__logo--img" src="{{asset('img/logo.svg')}}" alt="CoachTech">
            </div>
            <div class="header__buttons">
                @if (Auth::guard('admin')->check())
                    <a class="header__button--attendance" href="/admin/attendance/list">勤怠一覧</a>
                    <a class="header__button--list" href="/admin/staff/list">スタッフ一覧</a>
                    <a class="header__button--request" href="/stamp_correction_request/list">申請一覧</a>
                    <form method="POST" action="/admin/logout">
                        @csrf
                        <button class="header__button--logout" type="submit">ログアウト</button>
                    </form>
                @elseif(Auth::guard('users')->check())
                    <a class="header__button--attendance" href="/attendance">勤怠</a>
                    <a class="header__button--list" href="/attendance/list">勤怠一覧</a>
                    <a class="header__button--request" href="/stamp_correction_request/list">申請</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button class="header__button--logout" type="submit">ログアウト</button>
                    </form>
                @endif
            </div>
        </div>
    </header>
    <main class="content">
    @yield('content')
    </main>
</body>
</html>