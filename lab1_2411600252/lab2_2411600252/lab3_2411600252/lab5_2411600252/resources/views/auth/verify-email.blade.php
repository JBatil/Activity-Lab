@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
    <p class="text-muted small">
        Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.
        If you didn't receive the email, we'll gladly send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex justify-content-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Log Out</button>
        </form>
    </div>
@endsection
