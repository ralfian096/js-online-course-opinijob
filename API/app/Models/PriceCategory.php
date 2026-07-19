<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'category_id');
    }
}
