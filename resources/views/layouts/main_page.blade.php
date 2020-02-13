@extends('layouts.app')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container">
    <div class="row justify-content-center">
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
                </div>
            </div>
        </div>

        <div class="col-md-8">
        @yield('main_contents')
        </div>
    </div>
</div>
@endsection