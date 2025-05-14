@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approval.css') }}">
@endsection

@section('content')
<div class=title>
    <h2 class="title__text">
        勤怠詳細
    </h2>
</div>

<div class=content__table>
    <form class="form__correct" method="post" action="{{ route('admin.approval', ['id' => $new_attendance->attendance_id]) }}">
        @csrf
        <table class=table__detail>
            <colgroup>
                <col class="col__label">
                <col class="col__input-start">
                <col class="col__separator">
                <col class="col__input-end">
                <col class="col__extra">
            </colgroup>
            <tr class=table__row>
                <th class=table__header>
                    名前
                </th>
                <td class=table__data>
                    {{ $new_attendance['user']['name'] }}
                    <input type="hidden" name="new_attendance_id" value="{{ $new_attendance['id']}}" readonly />
                </td>
            </tr>
            <tr class=table__row>
                <th class=table__header>
                    日付
                </th>
                <td class=table__data>
                    {{ $new_attendance['new_clock_in']->format('Y年') }}
                </td>
                <td class="table__data">
                    &nbsp;
                </td>
                <td class=table__data>
                    {{ $new_attendance['new_clock_in']->format('n月j日') }}
                </td>
            </tr>
            <tr class=table__row>
                <th class=table__header>
                    出勤・勤怠
                </th>
                <td class=table__data>
                    <input type="time" name="new_clock_in" value="{{ $new_attendance['new_clock_in']->format('H:i')}}" readonly />
                </td>
                <td class=table__data>
                    〜
                </td>
                <td class=table__data>
                    <input type="time" name="new_clock_out" value="{{ $new_attendance['new_clock_out']->format('H:i')}}" readonly />
                </td>
            </tr>
            @foreach ($new_attendance->new_breaks as $i => $break)
                <tr class="table__row">
                    <th class="table__header">
                        休憩{{ $loop->iteration }}
                    </th>
                    <td class="table__data">
                        <input type="time" name="new_start_time" value="{{ $break['new_start_time']->format('H:i')}}" readonly />
                    </td>
                    <td class="table__data">
                        〜
                    </td>
                    <td class="table__data">
                        <input type="time" name="new_end_time" value="{{ $break['new_end_time']->format('H:i')}}" readonly />
                    </td>
                </tr>
            @endforeach
            <tr class="table__row">
                <th class="table__header">
                    休憩{{ $breaksCount + 1 }}
                </th>
            </tr>
            <tr class=table__row>
                <th class=table__header>
                    備考
                </th>
                <td class=table__data>
                    <input type="text" name="new_note" value="{{ $new_attendance['new_note']}}" readonly />
                </td>
            </tr>
        </table>
        @php
            use App\Models\NewAttendance;
        @endphp
        @if ($new_attendance['status'] == NewAttendance::STATUS_PENDING)
            <div class=button>
                <button class="form__button" type="submit">
                    承認
                </button>
            </div>
        @else
            <div class=text>
                <span class="approval__text">
                    承認済み
                </span>
            </div>
        @endif
    </form>
</div>
@endsection