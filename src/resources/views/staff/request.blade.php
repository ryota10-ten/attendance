@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request.css') }}">
@endsection

@section('content')

<div class=title>
    <h2 class="title__text">
        申請一覧
    </h2>
</div>

<div class="tab_wrap">
    <input id="tab1" type="radio" name="tab_btn" checked>
    <input id="tab2" type="radio" name="tab_btn">
    <div class="tab_area">
        <label class="tab1_label" for="tab1">承認待ち</label>
        <label class="tab2_label" for="tab2">承認済み</label>
    </div>
    <div class="panel_area">
        <div id="panel1" class="unapproved__list">
            <table class="table">
                <tr class="table__row">
                    <th class="table__header">
                        状態
                    </th>
                    <th class="table__header">
                        名前
                    </th>
                    <th class="table__header">
                        対象日時
                    </th>
                    <th class="table__header">
                        申請理由
                    </th>
                    <th class="table__header">
                        申請日時
                    </th>
                    <th class="table__header">
                        詳細
                    </th>
                </tr>
                @foreach($unapproved__lists as $unapproved__list)
                <tr class="table__row">
                    <td class="table__data">
                        承認待ち
                    </td>
                    <td class="table__data">
                        {{ $unapproved__list['user']['name'] }}
                    </td>
                    <td class="table__data">
                        {{ $unapproved__list['new_clock_in']->format('Y/m/d') }}
                    </td>
                    <td class="table__data">
                        {{ $unapproved__list['new_note'] }}
                    </td>
                    <td class="table__data">
                        {{ $unapproved__list['created_at']->format('Y/m/d') }}
                    </td>
                    <td class="table__data">
                        <a class="table__data" href="/stamp_correction_request/approve/{{ $unapproved__list['id'] }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <div id="panel2" class="approved__list">
            <table class="table">
                <tr class="table__row">
                    <th class="table__header">
                        状態
                    </th>
                    <th class="table__header">
                        名前
                    </th>
                    <th class="table__header">
                        対象日時
                    </th>
                    <th class="table__header">
                        申請理由
                    </th>
                    <th class="table__header">
                        申請日時
                    </th>
                    <th class="table__header">
                        詳細
                    </th>
                </tr>
                @foreach($approved__lists as $approved__list)
                <tr class="table__row">
                    <td class="table__data">
                        承認済み
                    </td>
                    <td class="table__data">
                        {{$approved__list['user']['name']}}
                    </td>
                    <td class="table__data">
                        {{ $approved__list['new_clock_in']->format('Y/m/d') }}
                    </td>
                    <td class="table__data">
                        {{ $approved__list['new_note'] }}
                    </td>
                    <td class="table__data">
                        {{ $approved__list['created_at']->format('Y/m/d') }}
                    </td>
                    <td class="table__data">
                        <a class="table__data" href="/attendance/{{ $approved__list['id'] }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

@endsection