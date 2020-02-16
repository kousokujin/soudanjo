@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">ユーザー一覧</div>
        <div class='card-body'>
            <table class="table table-striped scroll-table">
                <tr>
                    <th>userid</th>
                    <th>名前</th>
                    <th>作成日</th>
                    <th>アイコン</th>
                    <!--<th></th>-->

                </tr>
                @foreach($users as $u)
                <tr>
                    <td>
                        {{$u->userid}}
                    </td>
                    <td>
                        {{$u->name}}
                    </td>
                    <td>
                        {{$u->created_at}}
                    </td>
                    <td>
                        <a href="{{asset('storage/profile_images/'.$u->icon)}}">
                        {{$u->icon}}
                    </td>
                    <!--<td>
                        <a href="/admin_password_edit/{{$u->userid}}" class="btn btn-primary">パスワード変更</a>
                    </td>
                    -->
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection