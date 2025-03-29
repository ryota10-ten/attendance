@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
@endsection

@section('content')
<body class="body">
    @php
        use App\Models\Attendance;
    @endphp
    <div class="content__status">
        @if ($status === Attendance::NOT_STARTED)
            勤務外
        @elseif ($status === Attendance::WORKING)
            勤務中
        @elseif ($status === Attendance::ON_BREAK)
            休憩中
        @elseif ($status === Attendance::FINISHED)
            退勤済
        @endif
    </div>
    <div class="content__time">
        <p class="currentDate">{{ $date }}</p>
        <p class="currentTime">{{ $time }}</p>
    </div>
    <div class="content__form">
        @if ($status === Attendance::NOT_STARTED)
            <form action="{{ route('attendance.clock-in') }}" method="POST">
                @csrf
                <button class="attendance__button" type="submit">出勤</button>
            </form>
        @elseif ($status === Attendance::WORKING)
            <form action="{{ route('attendance.clock-out') }}" method="POST">
                @csrf
                <button class="attendance__button" type="submit">退勤</button>
            </form>
            <form action="{{ route('attendance.break-start') }}" method="POST">
                @csrf
                <button class="attendance__button--break" type="submit">休憩入</button>
            </form>
        @elseif ($status === Attendance::ON_BREAK)
            <form action="{{ route('attendance.break-end') }}" method="POST">
                @csrf
                <button class="attendance__button" type="submit">休憩戻</button>
            </form>
        @elseif ($status === Attendance::FINISHED)
            <h2 class="attendance__text">お疲れさまでした</h2>
        @endif
    </div>
</body>
@endsection