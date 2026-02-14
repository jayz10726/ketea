<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'serial_number',
        'condition',
        'location',
        'status',
        'purchase_date',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    // Auto-generate asset_code before creating
    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (empty($asset->asset_code)) {
                $prefix = strtoupper(substr($asset->category, 0, 3));
                $next   = (static::max('id') ?? 0) + 1;
                $asset->asset_code = $prefix . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issuances()
    {
        return $this->morphMany(Issuance::class, 'issuable');
    }

    public function currentIssuance()
    {
        return $this->issuances()->where('status', 'Issued')->latest()->first();
    }
}