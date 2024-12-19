<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;

class Tribe extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tribe_name',
        'description',
        'language',
        'cultural_practices',
        'population',
        'region',
        'url_image',
        'category_id',
        'user_id',
    ];

    /**
     * Get the user that owns the tribe.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the tribe belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Delete the tribe's image from storage.
     */
    public function deleteImage()
    {
        if ($this->url_image) {
            Storage::disk('public')->delete($this->url_image);
        }
    }

    /**
     * Get the tribe's image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return $this->url_image
            ? Storage::disk('public')->url($this->url_image)
            : asset('views/default/lawlaw.jpg');
    }

    /**
     * Scope a query to only include tribes of a given category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include tribes of the authenticated user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Get the tribe's preparation time in a human-readable format.
     *
     * @return string
     */
    public function getFormattedPrepTimeAttribute()
    {
        // Convert prep_time to integer in case it's stored as a string
        $totalMinutes = (int) $this->prep_time;

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $timeString = '';
        if ($hours > 0) {
            $timeString .= $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ';
        }
        if ($minutes > 0) {
            $timeString .= $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return trim($timeString) ?: 'N/A';
    }
}