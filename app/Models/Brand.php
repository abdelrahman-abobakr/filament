<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'primary_hex',
        'is_visible',
        'description'
    ];

    public function products(): HasMany
    {
        return $this-> hasMany(Product::class);
    }
}
