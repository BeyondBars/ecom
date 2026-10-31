<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Timeline entry on an order: status changes, payments, notes.
 */
class OrderEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['type', 'note', 'user_id', 'is_internal', 'created_at'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean', 'created_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
