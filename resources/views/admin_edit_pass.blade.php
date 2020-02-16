@extends('layouts.app')

@section('content')
    <div class="card margin-top">
        <div class="card-header">パスワード変更({{$id}})</div>

        <div class="card-body">
            <form method="POST" action="/admin_modify_password">
                @csrf
                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">新しいパスワード</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">新しいパスワード（再入力）</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                <input id='userid' type='hidden' value='{{$id}}'>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            変更
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection