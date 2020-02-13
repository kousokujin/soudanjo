@extends('layouts.app')

@section('scripts')
<script type="text/javascript">
    function CancelCheck() {
        var res = confirm("キャンセルしますか？");
        if( res == true ) {
            // OKなら移動
            window.location.href = "/cancel/{{$quest->id}}";
        }
    }
</script>
<script type="text/javascript">
    function go_cal(){
        var text = '{{$quest->name}}';
        var datefrom = '{{$start_time}}';
        var dateto = '{{$end_time}}';
        var detail = '{{$quest->comment}}';

        var zero = function(n) { return ('0' + n).slice(-2); };
        var formatdate = function(datestr) {
            var date = new Date(datestr + '+09:00');
            return date.getUTCFullYear() + zero(date.getUTCMonth()+1) + zero(date.getUTCDate()) + 'T' +
                zero(date.getUTCHours()) + zero(date.getUTCMinutes()) + zero(date.getUTCSeconds()) + 'Z';
        };
        var url = 'http://www.google.com/calendar/event?action=TEMPLATE' +
            '&text=' + encodeURIComponent(text) +
            '&dates=' + formatdate(datefrom) + '/' + formatdate(dateto) + 
            '&details=' + encodeURIComponent(detail);
        window.location.href= url;
    }
</script>
@endsection

@section('ogp')

<meta property="og:site_name" content="固定相談所" />
<meta property="og:title" content="{{$quest->party_name}}" />
<meta property="og:type" content="article" />
<meta property="og:description" content="{{$quest->comment}}" />
<meta property="og:image" content="https:///opg.png" />
<meta property="og:url" content="{{\Request::fullUrl()}}" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@sega_pso2" />

@endsection
@section('content')
    <div class="card margin-top">
        <div class="card-header">{{$quest->party_name}}</div>
        <div class='card-body'>
            <table class="table table-striped">
                <tr>
                    <th>主催者</th>
                    <td>
                        <img src="{{asset('storage/profile_images/'.$quest->icon)}}" class='icon-image'>
                        {{ $quest->name}}
                    </td>
                </tr>
                <tr>
                    <th>人数</th>
                    <td>{{ $quest->count }}/{{ $quest->max }}</td>
                </tr>
                <tr>
                    <th>参加締め切り</th>
                    <td>{{$quest->deadline}}</td>
                <tr>
                    <th>コメント</td>
                    <td>{!! nl2br(htmlspecialchars($quest->comment)) !!}</td>
                </tr>
            </table>

            @if(Auth::check() && $quest->userid == Auth::user()->userid)
                <a href="/edit/{{$quest->id}}" class="btn btn-primary">編集</a>
            @endif
            <!--<a id='cal' class="btn btn-primary" href="#" onclick="go_cal();">カレンダーに追加</a>-->
        </div>
    </div>

    <div class="card margin-top">
        <div class="card-header">参加者</div>
        <div class='card-body'>
            @if(count($member) == 0)
                だれもいません。<br />
                かなしい。。。
            @else
                <table class="table table-striped">
                    <tr>
                        <th id="name_table">名前</th>
                        <th id="class_table">クラス</th>
                        <th class="auto_width">コメント</th>
                    </tr>
                    @foreach($member as $m)
                    <tr>
                        <td>
                            <img src="{{asset('storage/profile_images/'.$m->icon)}}" class='icon-image'>
                            {{$m->name}}
                        </td>
                        <td>
                            @if($m->main_class == "none")
                                未定
                            @else
                            {{$m->main_class}}
                            @endif
                            @if($m->sub_class !== "none" and $m->main_class !=="Hr" and $m->main_class !=="Ph" and $m->main_class !=="Et" and $m->main_class !=="none")
                            {{$m->sub_class}}
                            @endif
                        
                        </td>
                        <td class="auto_width">{{$m->comment}}</td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
    
    @if(Auth::check())
    <div class="card margin-top">
        <div class='card-header'>参加/変更</div>
        <div class='card-body'>
            @if(($quest->count < $quest->max && $isdeadline == false)||($isjoin == true))
            <form method="POST" action="/quests/join" onsubmit="return check(this)">
                {{ csrf_field() }}
                <div class = "form-group row">
                    <label for="main_class" class="col-md-4 col-form-label text-md-right">メインクラス</label>
                    <div class="col-md-6">
                        <select id="main_class" class="form-control @error('main_class') is-invalid @enderror" name="main_class"  required autofocus>
                            <option value="none" @if($main_class == 'none') selected @endif>未定</option>
                            <option value="Hu" @if($main_class == 'Hu') selected @endif>ハンター</option>
                            <option value="Fi" @if($main_class == 'Fi') selected @endif>ファイター</option>
                            <option value="Ra" @if($main_class == 'Ra') selected @endif>レンジャー</option>
                            <option value="Gu" @if($main_class == 'Gu') selected @endif>ガンナー</option>
                            <option value="Fo" @if($main_class == 'Fo') selected @endif>フォース</option>
                            <option value="Te" @if($main_class == 'Te') selected @endif>テクター</option>
                            <option value="Br" @if($main_class == 'Br') selected @endif>ブレイバー</option>
                            <option value="Bo" @if($main_class == 'Br') selected @endif> バウンサー</option>
                            <option value="Su" @if($main_class == 'Su') selected @endif>サモナー</option>
                            <option value="Hr" @if($main_class == 'Hr') selected @endif>ヒーロー</option>
                            <option value="Ph" @if($main_class == 'Ph') selected @endif>ファントム</option>
                            <option value="Et" @if($main_class == 'Et') selected @endif>エトワール</option>
                        </select>

                    </div>
                </div>

                <div class = "form-group row">
                    <label for="sub_class" class="col-md-4 col-form-label text-md-right">サブクラス</label>
                    <div class="col-md-6">
                        <select id="sub_class" class="form-control @error('sub_class') is-invalid @enderror" name="sub_class"  required autofocus>
                            <option value="none" @if($sub_class == 'none') selected @endif>未定</option>
                            <option value="Hu" @if($sub_class == 'Hu') selected @endif>ハンター</option>
                            <option value="Fi" @if($sub_class == 'Fi') selected @endif>ファイター</option>
                            <option value="Ra" @if($sub_class == 'Ra') selected @endif>レンジャー</option>
                            <option value="Gu" @if($sub_class == 'Gu') selected @endif>ガンナー</option>
                            <option value="Fo" @if($sub_class == 'Fo') selected @endif>フォース</option>
                            <option value="Te" @if($sub_class == 'Te') selected @endif>テクター</option>
                            <option value="Br" @if($sub_class == 'Br') selected @endif>ブレイバー</option>
                            <option value="Bo" @if($sub_class == 'Br') selected @endif> バウンサー</option>
                            <option value="Su" @if($sub_class == 'Su') selected @endif>サモナー</option>
                            <option value="Ph" @if($sub_class == 'Ph') selected @endif>ファントム</option>
                            <option value="Et" @if($sub_class == 'Et') selected @endif>エトワール</option>
                        </select>
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">コメント</label>
                    <div class="col-md-6">
                        <textarea id="comment" type="text" class="form-control @error('comment') is-invalid @enderror" name="comment" value="{{ old('comment') }}" autofocus>{{$comment}}</textarea>

                        @error('comment')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <input type="hidden" name='id' value='{{$quest->id}}'>
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            @if($isjoin == true)
                                変更
                            @else
                                参加
                            @endif
                        </button>
                        @if($isjoin==true)
                        <a class="btn btn-primary btn-danger" href="#" onclick="CancelCheck();">参加取り消し</a>
                        @endif
                    </div>
                </div>

            </form>
            @else
                @if($isdeadline==true)
                <p>締め切りを過ぎました。</p>
                @else
                <p>定員に達しました</p>
                @endif
            @endif
        </div>
    </div>
    @endif
@endsection