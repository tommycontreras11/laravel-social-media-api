<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFriend extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id', 'target_id', 'type', 'status',
    ];

    public function source_friend(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'source_id');
    }

    public function target_friend(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
