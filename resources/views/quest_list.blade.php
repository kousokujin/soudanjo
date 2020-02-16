@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">イベント一覧</div>
        <div class='card-body'>
            <table class="table table-striped scroll-table">
                <tr>
                    <th>id</th>
                    <th>イベント名</th>
                    <th>主催者</th>
                    <th>IPアドレス</th>
                    <th>作成日</th>
                    <th>締め切り</th>

                </tr>
                @foreach($quests as $q)
                <tr>
                    <td>
                        {{$q->id}}
                    </td>
                    <td>
                        <a href='/quests/{{$q->id}}'>
                        {{$q->party_name}}
                        </a>
                    </td>
                   
                    <td>
                        {{$q->userid}}
                    </td>
                    <td>
                        {{$q->ip}}
                    </td> 
                    <td>
                        {{$q->created_at}}
                    </td>
                    <td>
                        {{$q->deadline}}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection