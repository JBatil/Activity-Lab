@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="small text-decoration-none" href="{{ route('password.request') }}">Forgot your password?</a>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="text-center mt-2">
                <small class="text-muted">No account? <a href="{{ route('register') }}">Register</a></small>
            </div>
        @endif
    </form>
@endsection
