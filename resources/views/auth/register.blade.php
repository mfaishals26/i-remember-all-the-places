@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-tape"></div>
        <span class="auth-eyebrow">START YOUR STORY</span>
        <h1>Create your memory space.</h1>
        <p class="muted">Buat akun lokal untuk mulai mengingat semua tempat.</p>
        <form method="POST" action="{{ route('register.store') }}" class="auth-form">
            @csrf
            <label>Nama<input name="name" value="{{ old('name') }}" required></label>
            <label>Email<input name="email" type="email" value="{{ old('email') }}" required></label>
            <label>Password<input name="password" type="password" required></label>
            <label>Konfirmasi Password<input name="password_confirmation" type="password" required></label>
            <button class="btn full" type="submit">Buat akun</button>
        </form>
        <p class="center">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
</div>
@endsection
