@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-tape"></div>
        <span class="auth-eyebrow">WELCOME BACK</span>
        <h1>Welcome back.</h1>
        <p class="muted">Masuk untuk menyimpan semua tempat yang pernah berarti.</p>
        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf
            <label>Email<input name="email" type="email" value="{{ old('email') }}" required autofocus></label>
            <label>Password<input name="password" type="password" required></label>
            <label class="check"><input type="checkbox" name="remember"> <span>Remember me</span></label>
            <button class="btn full" type="submit">Masuk</button>
        </form>
        <p class="center">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>
@endsection
