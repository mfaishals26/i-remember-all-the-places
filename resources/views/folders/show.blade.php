@extends('layouts.app')
@section('title', $folder->name)
@section('content')
<div class="album-page">
    <div class="album-topbar">
        <a href="{{ route('dashboard') }}" class="back-link">← Semua scrapbook</a>
        <span>{{ $folder->name }}</span>
    </div>

    <div class="album-book">
        <section class="album-left-page">
            <div class="page-tape"></div><div class="page-number">01</div>
            <div class="album-intro">
                <span class="eyebrow">MY SCRAPBOOK</span>
                <h1>{{ $folder->name }}</h1>
                @if($folder->description)<p class="album-description">{{ $folder->description }}</p>@endif
                <div class="album-divider">✦</div>
                <div class="album-stats">
                    <div><strong>{{ $folder->memories->count() }}</strong><span>memories</span></div>
                    <div><strong>{{ $folder->memories->flatMap(fn($memory) => $memory->photos)->count() }}</strong><span>photos</span></div>
                </div>
                @php $firstPhotos = $folder->memories->flatMap(fn($memory) => $memory->photos)->take(2); @endphp
                @if($firstPhotos->count())
                    <div class="left-photo-collage">
                        @foreach($firstPhotos as $index => $photo)
                            <div class="left-polaroid left-polaroid-{{ $index + 1 }}">
                                <div class="polaroid-image"><img src="{{ asset('storage/' . $photo->photo) }}" alt=""></div>
                                <span>memory</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="left-placeholder"><span>✦</span><p>Tempel foto pertama untuk memulai cerita.</p></div>
                @endif
                <div class="handwritten">memories worth keeping ♡</div>
            </div>
        </section>

        <section class="album-right-page">
            <div class="page-tape page-tape-right"></div>
            <div class="memory-form-paper">
                <div class="form-paper-title"><span>NEW MEMORY</span><h2>Tambahkan kenangan</h2></div>
                <form action="{{ route('memories.store', $folder) }}" method="POST" enctype="multipart/form-data" id="memoryForm">
                    @csrf
                    <div class="memory-form-group">
                        <label>Foto</label>
                        <label for="photos" class="photo-upload-box">
                            <div class="upload-icon">📷</div><strong>Pilih foto</strong><span>Bisa pilih banyak foto sekaligus</span><small>JPG, PNG, WEBP · maksimal 15 MB/foto · maksimal 30 foto</small>
                        </label>
                        <input type="file" name="photos[]" id="photos" accept="image/jpeg,image/png,image/webp" multiple hidden>
                        <div id="photoPreview" class="photo-preview-grid"></div>
                    </div>

                    <div class="memory-form-group"><label for="title">Judul memory</label><input type="text" name="title" id="title" placeholder="Contoh: Makan bareng di Go Grill" required></div>
                    <div class="memory-form-group"><label for="memory_date">Tanggal</label><input type="date" name="memory_date" id="memory_date" required></div>

                    <div class="memory-form-group">
                        <label for="locationSearch">Lokasi</label>
                        <div class="location-search-wrapper"><input type="text" id="locationSearch" placeholder="Cari tempat..." autocomplete="off"><div id="locationResults" class="location-results"></div></div>
                        <div id="selectedLocation" class="selected-location" style="display:none;"><span>📍</span><div><strong id="selectedPlaceName"></strong><small id="selectedAddress"></small></div></div>
                        <input type="hidden" name="place_name" id="place_name"><input type="hidden" name="address" id="address"><input type="hidden" name="latitude" id="latitude"><input type="hidden" name="longitude" id="longitude">
                    </div>

                    <div class="memory-form-group"><label for="note">Cerita</label><textarea name="note" id="note" rows="4" placeholder="Tulis sedikit cerita tentang hari ini..."></textarea></div>
                    <button type="submit" class="stick-memory-button">✦ Tempel ke scrapbook</button>
                </form>
            </div>

            <div class="memories-heading"><span>OUR MOMENTS</span><h2>Kenangan</h2></div>

            @if($folder->memories->count())
                <div class="memory-list">
                    @foreach($folder->memories as $index => $memory)
                        <article class="memory-entry memory-entry-{{ ($index % 4) + 1 }}">
                            <div class="memory-tape"></div>
                            <div class="memory-date"><span>{{ $memory->memory_date->format('d') }}</span><small>{{ strtoupper($memory->memory_date->format('M Y')) }}</small></div>
                            <div class="memory-content">
                                <h3>{{ $memory->title }}</h3>
                                @if($memory->photos->count())
                                    <div class="memory-photo-grid">
                                        @foreach($memory->photos as $photo)
                                            <div class="memory-photo"><img src="{{ asset('storage/' . $photo->photo) }}" alt="{{ $memory->title }}" loading="lazy"></div>
                                        @endforeach
                                    </div>
                                @elseif($memory->photo)
                                    <div class="memory-photo-grid"><div class="memory-photo"><img src="{{ asset('storage/' . $memory->photo) }}" alt="{{ $memory->title }}"></div></div>
                                @endif
                                @if($memory->place_name)
                                    <div class="memory-location"><span>📍</span><div><strong>{{ $memory->place_name }}</strong>@if($memory->address)<small>{{ $memory->address }}</small>@endif</div>@if($memory->map_url)<a href="{{ $memory->map_url }}" target="_blank" rel="noopener noreferrer">Maps ↗</a>@endif</div>
                                @endif
                                @if($memory->note)<p class="memory-note">“{{ $memory->note }}”</p>@endif
                                <form action="{{ route('memories.destroy', $memory) }}" method="POST" onsubmit="return confirm('Hapus memory ini beserta semua fotonya?')">
                                    @csrf @method('DELETE')<button type="submit" class="memory-delete">Hapus memory</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="no-memories"><span>♡</span><h3>Belum ada memory</h3><p>Tambahkan kenangan pertamamu menggunakan form di atas.</p></div>
            @endif
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const photosInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photoPreview');
    const form = document.getElementById('memoryForm');

    if (photosInput) {
        photosInput.addEventListener('change', () => {
            photoPreview.innerHTML = '';
            const files = Array.from(photosInput.files);
            if (files.length > 30) {
                alert('Maksimal 30 foto dalam satu memory.');
                photosInput.value = '';
                return;
            }
            files.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => {
                    const item = document.createElement('div');
                    item.className = 'preview-photo-item';
                    item.innerHTML = `<img src="${e.target.result}" alt=""><span>${index + 1}</span>`;
                    photoPreview.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    const locationSearch = document.getElementById('locationSearch');
    const locationResults = document.getElementById('locationResults');
    const selectedLocation = document.getElementById('selectedLocation');
    const selectedPlaceName = document.getElementById('selectedPlaceName');
    const selectedAddress = document.getElementById('selectedAddress');
    const placeNameInput = document.getElementById('place_name');
    const addressInput = document.getElementById('address');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    let searchTimeout = null;

    if (locationSearch) {
        locationSearch.addEventListener('input', () => {
            const query = locationSearch.value.trim();
            clearTimeout(searchTimeout);
            if (query.length < 2) {
                locationResults.innerHTML = '';
                locationResults.style.display = 'none';
                return;
            }
            searchTimeout = setTimeout(() => searchLocation(query), 500);
        });
    }

    async function searchLocation(query) {
        locationResults.innerHTML = '<div class="location-loading">Mencari lokasi...</div>';
        locationResults.style.display = 'block';
        try {
            const response = await fetch(`{{ route('location.search') }}?q=${encodeURIComponent(query)}`);
            if (!response.ok) throw new Error('Location search failed');
            const places = await response.json();
            locationResults.innerHTML = '';
            if (!places.length) {
                locationResults.innerHTML = '<div class="location-empty">Lokasi tidak ditemukan.</div>';
                return;
            }
            places.forEach(place => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'location-result-item';
                const pin = document.createElement('span');
                pin.className = 'result-pin';
                pin.textContent = '📍';
                const text = document.createElement('span');
                text.textContent = place.place_name || '';
                item.append(pin, text);
                item.addEventListener('click', () => selectLocation(place));
                locationResults.appendChild(item);
            });
        } catch (error) {
            locationResults.innerHTML = '<div class="location-empty">Gagal mencari lokasi.</div>';
        }
    }

    function selectLocation(place) {
        placeNameInput.value = place.place_name || '';
        addressInput.value = place.address || '';
        latitudeInput.value = place.latitude || '';
        longitudeInput.value = place.longitude || '';
        selectedPlaceName.textContent = place.place_name || '';
        selectedAddress.textContent = place.address || '';
        selectedLocation.style.display = 'flex';
        locationSearch.value = place.place_name || '';
        locationResults.innerHTML = '';
        locationResults.style.display = 'none';
    }

    document.addEventListener('click', event => {
        if (locationSearch && !locationSearch.contains(event.target) && !locationResults.contains(event.target)) {
            locationResults.style.display = 'none';
        }
    });
});
</script>
@endsection
