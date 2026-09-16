<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_number',
        'unit',
        'stock',
        'minimum_stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * True kalau stok sudah di bawah atau sama dengan batas minimum,
     * dipakai untuk kasih peringatan visual di tampilan.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }
}
