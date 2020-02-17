@extends('layouts.app')
@section('scripts')
<script type="text/javascript">
    function CancelCheck() {
        var res = confirm("イベント削除しますか？");
        if( res == true ) {
            // OKなら移動
            window.location.href = "/delete/{{$quest->id}}";
        }
    }
</script>
@endsection
@section('content')
    <div class="card margin-top">
        <div class="card-header">イベントの編集</div>

        <div class="card-body">
            <form method="POST" action="/event_modify" onsubmit="return check(this)">
                {{ csrf_field() }}
                <div class = "form-group row">
                    <label for="quest_name" class="col-md-4 col-form-label text-md-right">イベント名</label>
                    <div class="col-md-6">
                        <input id="quest_name" value="{{$quest->party_name}}" type="text" class="form-control @error('quest_name') is-invalid @enderror" name="quest_name" value="{{ old('quest_name') }}" required autofocus>

                        @error('quest_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="max_member" class="col-md-4 col-form-label text-md-right">人数</label>
                    <div class="col-md-6">
                        <input id="max_member" type="text" value="{{$quest->max}}" class="form-control @error('max_member') is-invalid @enderror" name="max_member" value="{{ old('max_member') }}" required autofocus>

                        @error('max_member')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="date" class="col-md-4 col-form-label text-md-right">締め切り</label>
                    <div class="col-md-6">
                        <input id="date" type="date" value="{{$date}}" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" required autofocus>
                        <input id="time" type="time" value="{{$time}}" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time') }}" required autofocus>

                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="start_date" class="col-md-4 col-form-label text-md-right">開始予定時刻(オプション)</label>
                    <div class="col-md-6">

                        <input id="start_date"  @if($start_date != null) value="{{$start_date}}" @endif type="date" class="form-control @error('date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}" required autofocus>

                        <input id="start_time" @if($start_time != null) value="{{$start_time}}" @endif type="time" class="form-control @error('time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required autofocus>

                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="end_date" class="col-md-4 col-form-label text-md-right">終了予定時刻(オプション)</label>
                    <div class="col-md-6">
                        <input id="end_date" @if($end_date != null) value="{{$end_date}}" @endif type="date" class="form-control @error('date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" required autofocus>
                        <input id="end_time" @if($end_time != null) value="{{$end_time}}" @endif type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required autofocus>

                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">コメント</label>
                    <div class="col-md-6">
                        <textarea id="comment" type="text" class="form-control @error('comment') is-invalid @enderror" name="comment" value="{{ old('comment') }}" autofocus>{{$quest->comment}}</textarea>

                        @error('comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <input type="hidden" name='id' value="{{$quest->id}}">

                

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            変更
                        </button>
                        <a onclick="CancelCheck();" href="#" class="btn btn-danger btn-primary">イベント削除</a>
                    </div>
                </div>

            </form>
        </div>

    </div>

@endsection
