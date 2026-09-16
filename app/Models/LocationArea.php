<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationArea extends Model
{
    use HasFactory;

    protected $fillable = ['floor_id', 'name', 'code'];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
