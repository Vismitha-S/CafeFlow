<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CafeTable extends Model
{
    /** @use HasFactory<\Database\Factories\CafeTableFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }
}
