@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class=title>
    <h2 class="title__text">
        勤怠詳細
    </h2>
</div>

<div class=content__table>
    <form method="post" action="{{ auth('admin')->check() ? route('admin.update', ['id' => $attendance->id]) : route('staff.application', ['id' => $attendance->id]) }}">
        @csrf
        @if ($errors->any())
            <div class="form__errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
                    {{ $staff['name'] }}
                </td>
            </tr>
            <tr class=table__row>
                <th class=table__header>
                    日付
                </th>
                <td class=table__data>
                    {{ $attendance['clock_in']->format('Y年') }}
                </td>
                <td class="table__data">
                    &nbsp;
                </td>
                <td class=table__data>
                    {{ $attendance['clock_in']->format('n月j日') }}
                </td>
            </tr>
            <tr class=table__row>
                <th class=table__header>
                    出勤・勤怠
                </th>
                <td class=table__data>
                    @if ($pendingFixRequest)
                        {{ $new_attendance['new_clock_in']->format('H:i')}}
                    @else
                        <input type="time" class="icon-del" name="new_clock_in"
                        value="{{ old('new_clock_in',$attendance['clock_in']->format('H:i')) }}">
                    @endif
                </td>
                <td class=table__data>
                    〜
                </td>
                <td class=table__data>
                    @if ($pendingFixRequest)
                        {{ $new_attendance['new_clock_out']->format('H:i')}}
                    @else
                        <input type="time" class="icon-del" name="new_clock_out"
                        value="{{ old('new_clock_out', $attendance['clock_out']->format('H:i')) }}">
                    @endif
                </td>
            </tr>
            @if ($pendingFixRequest)
                @if ($new_attendance->new_breaks->isNotEmpty())
                    @foreach ($new_attendance->new_breaks as $i => $new_break)
                        <tr class="table__row">
                            <th class="table__header">
                                休憩{{ $loop->iteration }}
                            </th>
                            <td class="table__data">
                                {{ optional($new_break['new_start_time'])->format('H:i')}}
                            </td>
                            <td class="table__data">
                                〜
                            </td>
                            <td class="table__data">
                                {{ optional($new_break['new_end_time'])->format('H:i')}}
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr class=table__row>
                    <th class=table__header>
                        備考
                    </th>
                    <td class=table__data>
                        {{ $new_attendance['new_note']}}
                    </td>
                </tr>
            @else
                @foreach ($attendance->breaks as $i => $break)
                    <tr class="table__row">
                        <th class="table__header">
                            休憩{{ $loop->iteration }}
                            <input type="hidden" name="new_breaks[{{ $i }}][break_id]">
                        </th>
                        <td class="table__data">
                            <input type="time" class="icon-del" name="new_breaks[{{ $i }}][start_time]" value="{{ old('new_breaks.$i.start_time', $break['start_time']->format('H:i')) }}">
                        </td>
                        <td class="table__data">
                            〜
                        </td>
                        <td class="table__data">
                            <input type="time" class="icon-del" name="new_breaks[{{ $i }}][end_time]" value="{{ old('new_breaks.$i.end_time', $break['end_time']->format('H:i')) }}">
                        </td>
                    </tr>
                @endforeach
                <tr class="table__row">
                    <th class="table__header">
                        休憩{{ $breaksCount + 1 }}
                        <input type="hidden" name="new_breaks_id[{{ $breaksCount + 1 }}]">
                    </th>
                    <td class="table__data">
                        <input type="time" class="icon-del" name="new_breaks_add[{{ $breaksCount + 1 }}][start_time]" value="">
                    </td>
                    <td class="table__data">
                        〜
                    </td>
                    <td class="table__data">
                        <input type="time" class="icon-del" name="new_breaks_add[{{ $breaksCount + 1 }}][end_time]" value="">
                    </td>
                </tr>
                <tr class=table__row>
                    <th class=table__header>
                        備考
                    </th>
                    <td class=table__data colspan="3">
                        <textarea name="new_note" class="note" rows="3">{{ $attendance['note'] }}</textarea>
                    </td>
                </tr>
            @endif
        </table>
        @if ($pendingFixRequest)
            <div class=text>
                *承認待ちのため修正はできません。
            </div>
        @else
            <div class=button>
                <button class="form__button" type="submit">
                    修正
                </button>
            </div>
        @endif
    </form>
</div>
@endsection