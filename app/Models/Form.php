<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'fields', 'is_active', 'start_at', 'end_at',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}

