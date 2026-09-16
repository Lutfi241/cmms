<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'asset_category_id',
        'location_area_id',
        'status',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function locationArea(): BelongsTo
    {
        return $this->belongsTo(LocationArea::class);
    }

    /**
     * Generate kode aset otomatis, format: AST-0001, AST-0002, dst.
     * Dipakai di Controller sebelum create(), bukan di boot(),
     * supaya lebih mudah di-test dan tidak "magic".
     */
    public static function generateAssetCode(): string
    {
        $last = static::orderByDesc('id')->first();
        $nextNumber = $last ? ((int) substr($last->asset_code, 4)) + 1 : 1;

        return 'AST-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
