@extends('layouts.app')

@section('content')
    <h3>募集中のイベント一覧</h3>
    @if(count($quests)==0)
        ないないよー(´・ω・｀)
    @else
    <div class="card-deck">
        @foreach($quests as $q)
            <div class="col-md-4">
                <div class="card">
                    <div class='card-body'>
                        <h5 class="card-title">{{\Illuminate\Support\Str::limit($q->party_name, $limit = 20, $end = '...')}}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">by {{$q->name}}</h6>
                        <p class="card-text">{{ \Illuminate\Support\Str::limit($q->comment, $limit = 40, $end = '...')}}</p>
                        <a href="/quests/{{ $q->id}}" class="btn btn-primary">詳しく</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
@endsection