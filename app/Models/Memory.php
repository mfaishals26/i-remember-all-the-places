<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'title',
        'note',
        'memory_date',
        'place_name',
        'address',
        'latitude',
        'longitude',
        'map_url',
        'photo',
    ];

    protected $casts = [
        'memory_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function photos()
    {
        return $this->hasMany(MemoryPhoto::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
