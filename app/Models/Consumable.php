<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity_in_stock',
        'reorder_level',
        'unit',
        'created_by',
        'notes',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issuances()
    {
        return $this->morphMany(Issuance::class, 'issuable');
    }

    // Helper: check if below reorder level
    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }
}