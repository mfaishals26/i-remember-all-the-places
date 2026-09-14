<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }
}
