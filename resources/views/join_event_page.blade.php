@extends('layouts.app')

@section('content')
<div class="card margin-top">
        <div class='card-header'>外部の人の参加</div>
        <div class='card-body'>
            @if(($quest->count < $quest->max)||($isjoin == true))
            <form method="POST" action="/quests/outuser_join" onsubmit="return check(this)">
                {{ csrf_field() }}

                <div class = "form-group row">
                    <label for="display_name" class="col-md-4 col-form-label text-md-right">名前</label>
                    <div class="col-md-6">
                        <input id="display_name" type="text" class="form-control @error('display_name') is-invalid @enderror" name="display_name" value="{{ old('display_name') }}" required autofocus>

                        @error('display_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class = "form-group row">
                    <label for="main_class" class="col-md-4 col-form-label text-md-right">メインクラス</label>
                    <div class="col-md-6">
                        <select id="main_class" class="form-control @error('main_class') is-invalid @enderror" name="main_class"  required autofocus>
                            <option value="none" selected>未定</option>
                            <option value="Hu">ハンター</option>
                            <option value="Fi">ファイター</option>
                            <option value="Ra">レンジャー</option>
                            <option value="Gu">ガンナー</option>
                            <option value="Fo">フォース</option>
                            <option value="Te">テクター</option>
                            <option value="Br">ブレイバー</option>
                            <option value="Bo"> バウンサー</option>
                            <option value="Su">サモナー</option>
                            <option value="Hr">ヒーロー</option>
                            <option value="Ph">ファントム</option>
                            <option value="Et">エトワール</option>
                        </select>

                    </div>
                </div>

                <div class = "form-group row">
                    <label for="sub_class" class="col-md-4 col-form-label text-md-right">サブクラス</label>
                    <div class="col-md-6">
                        <select id="sub_class" class="form-control @error('sub_class') is-invalid @enderror" name="sub_class"  required autofocus>
                            <option value="none" selected>未定</option>
                            <option value="Hu">ハンター</option>
                            <option value="Fi">ファイター</option>
                            <option value="Ra">レンジャー</option>
                            <option value="Gu">ガンナー</option>
                            <option value="Fo">フォース</option>
                            <option value="Te">テクター</option>
                            <option value="Br">ブレイバー</option>
                            <option value="Bo"> バウンサー</option>
                            <option value="Su">サモナー</option>
                            <option value="Ph">ファントム</option>
                            <option value="Et">エトワール</option>
                        </select>
                    </div>
                </div>

                <div class = "form-group row">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">コメント</label>
                    <div class="col-md-6">
                        <textarea id="comment" type="text" class="form-control @error('comment') is-invalid @enderror" name="comment" value="{{ old('comment') }}" autofocus></textarea>

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
                                参加
                        </button>
                    </div>
                </div>

            </form>
            @else
                <p>定員に達しました</p>
            @endif
        </div>
    </div>

@endsection