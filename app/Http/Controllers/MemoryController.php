<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Memory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MemoryController extends Controller
{
    public function searchLocation(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:200',
        ]);

        $response = Http::withHeaders([
            'User-Agent' => 'i-remember-all-the-places/1.0',
            'Accept-Language' => 'id',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $request->q,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 5,
            'countrycodes' => 'id',
        ]);

        if (!$response->successful()) {
            return response()->json(['message' => 'Pencarian lokasi gagal.'], 500);
        }

        return response()->json(
            collect($response->json())->map(function ($place) {
                return [
                    'place_name' => $place['display_name'] ?? 'Unknown place',
                    'address' => $place['display_name'] ?? null,
                    'latitude' => $place['lat'] ?? null,
                    'longitude' => $place['lon'] ?? null,
                ];
            })->values()
        );
    }

    public function store(Request $request, Folder $folder)
    {
        abort_unless($folder->user_id === auth()->id(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:5000'],
            'memory_date' => ['required', 'date'],
            'place_name' => ['nullable', 'string', 'max:500'],
            'address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'photos' => ['nullable', 'array', 'max:30'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ]);

        if (isset($data['latitude']) && isset($data['longitude'])) {
            $data['map_url'] = 'https://www.google.com/maps/search/?api=1&query=' . $data['latitude'] . ',' . $data['longitude'];
        } else {
            $data['map_url'] = null;
        }

        unset($data['photos']);

        $memory = $folder->memories()->create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('memories', 'supabase');
                $memory->photos()->create([
                    'photo' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('folders.show', $folder)->with('success', 'Memory berhasil ditempel ke scrapbook ✦');
    }

    public function destroy(Memory $memory)
    {
        abort_unless($memory->folder->user_id === auth()->id(), 403);

        foreach ($memory->photos as $photo) {
            if ($photo->photo) {
                Storage::disk('supabase')->delete($photo->photo);
            }
        }

        if ($memory->photo) {
            Storage::disk('public')->delete($memory->photo);
        }

        $memory->delete();

        return back()->with('success', 'Memory dihapus.');
    }
}
