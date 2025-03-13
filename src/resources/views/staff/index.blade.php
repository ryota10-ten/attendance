<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachTech</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/index.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
    <title>CoachTech</title>
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img class="header__logo--img" src="{{asset('img/logo.svg')}}" alt="CoachTech">
            </div>
            <div class="header__buttons">
                <a class="header__button--attendance" href="/attendance">勤怠</a>
                <a class="header__button--list" href="/attendance/list">勤怠一覧</a>
                <a class="header__button--request" href="/stamp_correction_request/list">申請</a>
                <form method="POST" action="/logout">
                    <button class="header__button--logout" type="submit">ログアウト</button>
                </form>
            </div>
        </div>
    </header>
    <main class="content">
        <div class="content__status">
            勤務外
        </div>
        <div class="content__time">
            <p class="currentDate">{{ $date }}</p>
            <p class="currentTime">{{ $time }}</p>
        </div>
        <div class="content__form">
            @if ($attendance)
                @if ($attendance->status == 'not_started')
                    <form action="{{ route('attendance.clock-in') }}" method="POST">
                        @csrf
                        <button class="attendance__button" type="submit">
                            出勤
                        </button>
                    </form>
                @elseif ($attendance->status == 'working')
                    <form action="{{ route('attendance.clock-out') }}" method="POST">
                        @csrf
                        <button class="attendance__button" type="submit">
                            退勤
                        </button>
                    </form>
                    <form action="{{ route('attendance.break-start') }}" method="POST">
                        @csrf
                        <button class="attendance__button" type="submit">
                            休憩入
                        </button>
                    </form>
                @elseif ($attendance->status == 'on_break')
                    <form action="{{ route('attendance.break-end') }}" method="POST">
                        @csrf
                        <button class="attendance__button" type="submit">
                            休憩戻
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </main>
</body>
</html>