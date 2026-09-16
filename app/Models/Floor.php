<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'name', 'code'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function locationAreas(): HasMany
    {
        return $this->hasMany(LocationArea::class);
    }
}
