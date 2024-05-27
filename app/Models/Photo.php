<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id', 'file_path', 'device', 'latitude', 'longitude', 'taken_at',
        'mime_type', 'width', 'height', 'description', 'make', 'model', 'iso_speed',
        'focal_length', 'software', 'additional_metadata'
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'additional_metadata' => 'array',
    ];
}
