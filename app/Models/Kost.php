<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'location',
        'google_maps_link',
        'type',
        'max_occupants',
        'price',
        'facilities',
        'images',
        'is_active',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'contact_whatsapp',
        'contact_instagram',
        'contact_facebook',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // Scope untuk kost yang sudah diapprove
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    // Scope untuk kost pending
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }
}