@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/list.css') }}">
@endsection

@section('content')
<div class=title>
    <h2 class="title__text">
        勤怠一覧
    </h2>
</div>
<div class=calender>
    <form method="POST" action="{{ route('staff.changeDate') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $selectedMonth }}">
        <button type="submit" name="action" value="prev" class="calender__before">
            ←前月
        </button>
    </form>
    <div class="calender__date">
        <form method="POST" action="{{ route('staff.changeDate') }}">
            @csrf
            <label class="calender__label">
                <img class="calender__icon" src="{{ asset('img/calender.png') }}" alt="カレンダーアイコン" onclick="openCalendar()">
                <input type="month" name="month" value="{{ old('month', $selectedMonth ?? now()->format('Y-m')) }}" id="datePicker" class="calender__input" onchange="this.form.submit()">
            </label>
        </form>
    </div>
    <form method="POST" action="{{ route('staff.changeDate') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $selectedMonth }}">
        <button type="submit" name="action" value="next" class="calender__after">
            翌月→
        </button>
    </form>
</div>
<div class="table">
    <table class="attendance__table">
        <tr>
            <th class="table__header">
                日付
            </th>
            <th class="table__header">
                出勤
            </th>
            <th class="table__header">
                退勤
            </th>
            <th class="table__header">
                休憩
            </th>
            <th class="table__header">
                合計
            </th>
            <th class="table__header">
                詳細
            </th>
        </tr>
        @foreach ($attendances as $attendance)
        <tr>
            <td class="table__data">
                {{ $attendance['date'] }}
            </td>
            <td class="table__data">
                {{ $attendance['clock_in'] }}
            </th>
            <td class="table__data">
                {{ $attendance['clock_out'] ?? '-' }}
            </th>
            <td class="table__data">
                {{ $attendance['break_time'] }}
            </th>
            <td class="table__data">
                {{ $attendance['work_time'] }}
            </th>
            <td class="table__data">
                <a class="table__data" href="/attendance/{{ $attendance['id'] }}">
                    詳細
                </a>
            </th>
        </tr>
        @endforeach
    </table>
</div>
<script>
    function openCalendar() {
        document.getElementById('datePicker').showPicker();
    }
</script>
@endsection