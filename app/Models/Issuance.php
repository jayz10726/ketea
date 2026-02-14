<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'issuance_code',
        'issuable_type',
        'issuable_id',
        'issued_to',
        'issued_by',
        'quantity',
        'issue_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'remarks',
    ];

    protected $casts = [
        'issue_date'           => 'date',
        'expected_return_date' => 'date',
        'actual_return_date'   => 'date',
    ];

    // Auto-generate issuance_code before creating
    protected static function booted(): void
    {
        static::creating(function (Issuance $issuance) {
            $issuance->issuance_code = 'ISS-' . strtoupper(uniqid());
        });
    }

    // Relationships
    public function issuable()
    {
        return $this->morphTo();
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}