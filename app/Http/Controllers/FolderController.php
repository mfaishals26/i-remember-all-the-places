<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index()
    {
        $folders = auth()->user()
            ->folders()
            ->with([
                'memories' => function ($query) {
                    $query->latest('memory_date')->with('photos');
                }
            ])
            ->withCount('memories')
            ->latest()
            ->get();

        return view('dashboard', compact('folders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        auth()->user()->folders()->create($data);

        return back()->with('success', 'Scrapbook berhasil dibuat ✨');
    }

    public function show(Folder $folder)
    {
        abort_unless($folder->user_id === auth()->id(), 403);

        $folder->load([
            'memories' => function ($query) {
                $query->latest('memory_date')->with('photos');
            }
        ]);

        return view('folders.show', compact('folder'));
    }

    public function destroy(Folder $folder)
    {
        abort_unless($folder->user_id === auth()->id(), 403);

        $folder->delete();

        return redirect()->route('dashboard')->with('success', 'Scrapbook berhasil dihapus.');
    }
}
