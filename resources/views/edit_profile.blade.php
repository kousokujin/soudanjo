@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">プロフィール編集</div>

        <div class="card-body">
            <form method="POST" action="/modify_profile" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>

                    <div class="col-md-6">
                        <input id="name" value="{{Auth::user()->name}}"type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="icon" class="col-md-4 col-form-label text-md-right">アイコン</label>

                    <div class="col-md-6">
                        <input id="icon" type="file" class="form-control @error('icon') is-invalid @enderror" name="icon" value="{{ old('icon') }}">

                        @error('icon')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            変更
                        </button>
                        <a href="/password/{{Auth::user()->userid}}" class="btn btn-primary">パスワード変更</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection