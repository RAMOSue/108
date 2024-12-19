<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
    ];

    /**
     * Get the tribes for the category.
     */
    public function tribes()
    {
        return $this->hasMany(Tribe::class);
    }
}