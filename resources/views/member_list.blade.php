@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">参加メンバー一覧</div>
        <div class='card-body'>
            <table class="table table-striped scroll-table">
                <tr>
                    <th>id</th>
                    <th>userid</th>
                    <th>quest_id</th>
                    <th>クラス</th>
                    <th>IPアドレス</th>
                    <th>参加日</th>

                </tr>
                @foreach($members as $m)
                <tr>
                    <td>
                        {{$m->id}}
                    </td>
                    <td>
                        {{$m->userid}}
                    </td>
                   
                    <td>
                        <a href='/quests/{{$m->quest_id}}'>
                        {{$m->quest_id}}
                        </a>
                    </td>
                    <td>
                        {{$m->main_class}}/{{$m->sub_class}}
                    </td> 
                    <td>
                        {{$m->ip}}
                    </td>
                    <td>
                        {{$m->created_at}}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection