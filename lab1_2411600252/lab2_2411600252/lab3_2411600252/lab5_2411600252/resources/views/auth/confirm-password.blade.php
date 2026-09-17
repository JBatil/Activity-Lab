@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
    <p class="text-muted small">This is a secure area. Please confirm your password before continuing.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Confirm</button>
    </form>
@endsection
