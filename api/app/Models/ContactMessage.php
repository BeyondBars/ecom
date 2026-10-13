<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = ['new', 'read', 'replied', 'archived'];

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'status'];
}
