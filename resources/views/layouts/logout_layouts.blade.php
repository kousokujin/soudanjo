@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        @if( Auth::check())
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">プロフィール</div>
                <div class="card-body">
                    <table class=table table-striped>
                        <tr>
                            <td>名前</td>
                            <th>{{ $auths->name}}</th>
                        </tr>
                        <tr>
                            <td>User ID</td>
                            <th>{{ $auths->userid}}</th>
                        </tr>
                    </table>
                    <a href="/edit_profile/{{ $auths->userid }}" class="btn btn-primary">編集</a>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-8">
        @yield('main_contents')
        </div>
    </div>
</div>
@endsection