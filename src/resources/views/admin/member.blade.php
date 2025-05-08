@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/member.css') }}">
@endsection

@section('content')

<div class=title>
    <h2 class="title__text">
        スタッフ一覧
    </h2>
</div>
<div class=list>
    <table class=list__table>
        <tr class=table__row--header>
            <th class=table__header>
                名前
            </th>
            <th class=table__header>
                メールアドレス
            </th>
            <th class=table__header>
                月次勤怠
            </th>
        </tr>
        @foreach ($staffs as $staff)
            <tr class=table__row>
                <td class=table__data>
                    {{ $staff['name'] }}
                </td>
                <td class=table__data>
                    {{ $staff['email'] }}
                </td>
                <td class=table__data>
                    <a class="table__data--detail" href="/admin/attendance/staff/{{ $staff['id'] }}">
                        詳細
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
</div>

@endsection