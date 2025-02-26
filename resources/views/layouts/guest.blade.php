@extends('layouts.clean')

@section('body-class', 'hold-transition login-page')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>My</b>App</a>
            </div>
            <div class="card-body">
                @yield('guest-content')
            </div>
        </div>
    </div>
@endsection
