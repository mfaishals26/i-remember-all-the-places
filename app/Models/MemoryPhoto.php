<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MemoryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'memory_id',
        'photo',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'url',
    ];

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return Storage::disk('supabase')->temporaryUrl(
            $this->photo,
            now()->addMinutes(60)
        );
    }
}