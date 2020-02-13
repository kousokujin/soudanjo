@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">参加したイベント</div>
        <div class="card-body">
            @if(count($joined_events)==0)
            ないないよー(´・ω・｀)
            @else
                <table class="table table-striped">
                    <tr>
                        <th>イベント名</th>
                        <th>主催者</th>
                        <th>人数</th>
                        <th>締め切り</th>
                        <th></th>
                    </tr>
                    @foreach($joined_events as $e)
                    <tr>
                        <td>{{$e->party_name}}</td>
                        <td>{{$e->name}}</td>
                        <td>{{ $e->count }}/{{ $e->max }}</td>
                        <td>{{$e->deadline}}</td>
                        <td>
                            <a href="/quests/{{ $e->quest_id}}" class="btn btn-primary">詳細</a>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
    <div class="card margin-top">
        <div class="card-header">主催したイベント</div>
        <div class="card-body">
        @if(count($mastar_event)==0)
            <p>主催しているイベントがありません。</p>
            <p>新しいイベントを作成しよう。</p>
                <a class = "btn btn-primary" href="/new_quest_wanted">作成</a>
        @else
            <table class="table table-striped">
                <tr>
                    <th>イベント名</th>
                    <th>人数</th>
                    <th>締め切り</th>
                    <th></th>
                </tr>
                @foreach($mastar_event as $m)
                <tr>
                    <td>{{$m->party_name}}</td>
                    <td>{{ $m->count }}/{{ $m->max }}</td>
                    <td>{{$m->deadline}}</td>
                    <td>
                        <a href="/quests/{{ $m->id}}" class="btn btn-primary">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
        </div>
    </div>
@endsection
