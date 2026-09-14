@extends('layouts.app')
@section('title', 'My Scrapbooks')
@section('content')
<div class="scrapbook-dashboard">
    <section class="scrapbook-hero">
        <div class="hero-tape"></div>
        <div class="hero-content">
            <span class="hero-small">MY MEMORY BOOK</span>
            <h1>i remember<br><span>all the places.</span></h1>
            <p>Simpan tempat, tanggal, foto, dan cerita yang ingin kamu ingat.</p>
        </div>
        <div class="hero-polaroid hero-polaroid-one"><div class="polaroid-placeholder">✦</div></div>
        <div class="hero-polaroid hero-polaroid-two"><div class="polaroid-placeholder">♡</div></div>
    </section>

    <section class="create-paper">
        <div class="paper-tape"></div>
        <div class="paper-heading"><span>NEW PAGE</span><h2>Buat scrapbook baru</h2></div>
        <form action="{{ route('folders.store') }}" method="POST" class="scrapbook-create-form">
            @csrf
            <div class="form-group"><label>Nama scrapbook</label><input type="text" name="name" placeholder="Contoh: Jalan-jalan 2026" required></div>
            <div class="form-group"><label>Cerita singkat</label><textarea name="description" rows="2" placeholder="Apa isi scrapbook ini?"></textarea></div>
            <button class="scrapbook-button" type="submit">+ Buat scrapbook</button>
        </form>
    </section>

    <section class="scrapbook-section">
        <div class="section-title"><div><span>YOUR COLLECTION</span><h2>Scrapbook saya</h2></div><div class="section-count">{{ $folders->count() }} scrapbook</div></div>
        @if($folders->count())
            <div class="scrapbook-grid">
                @foreach($folders as $folder)
                    @php
                        $previewPhotos = $folder->memories->flatMap(fn($memory) => $memory->photos)->take(3);
                        $coverPhoto = $previewPhotos->first();
                    @endphp
                    <article class="scrapbook-card">
                        <div class="card-tape"></div>
                        <a href="{{ route('folders.show', $folder) }}" class="scrapbook-cover">
                            @if($coverPhoto)
                                <img src="{{ $coverPhoto->url }}" alt="{{ $folder->name }}">
                            @else
                                <div class="empty-cover"><span>✦</span><small>YOUR STORY</small></div>
                            @endif
                            <div class="cover-label"><small>SCRAPBOOK</small><strong>{{ $folder->name }}</strong></div>
                        </a>
                        @if($previewPhotos->count() > 1)
                            <div class="mini-photo-stack">
                                @foreach($previewPhotos->skip(1) as $photo)
                                    <div class="mini-photo"><img src="{{ $photo->url }}" alt=""></div>
                                @endforeach
                            </div>
                        @endif
                        <div class="scrapbook-card-info">
                            <div><h3>{{ $folder->name }}</h3>@if($folder->description)<p>{{ $folder->description }}</p>@endif</div>
                            <div class="memory-count">{{ $folder->memories_count }}<span>{{ $folder->memories_count == 1 ? 'memory' : 'memories' }}</span></div>
                        </div>
                        <form action="{{ route('folders.destroy', $folder) }}" method="POST" class="delete-folder-form" onsubmit="return confirm('Hapus scrapbook ini beserta semua memory di dalamnya?')">
                            @csrf @method('DELETE')<button type="submit" class="small-delete" title="Hapus scrapbook">×</button>
                        </form>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-scrapbook"><div class="empty-sticker">♡</div><h3>Belum ada scrapbook</h3><p>Mulai simpan tempat-tempat yang ingin kamu ingat.</p></div>
        @endif
    </section>
</div>
@endsection
