<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'category_id', 'content', 'image', 'price', 'qty', 'status_id'
    ];

    /**
     * category
     * 
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * user
     * 
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * user
     * 
     * @return void
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }


    /**
     * image
     * Accessor > getter
     * @return string
     */
    public function getImageAttribute($image)
{
    return asset('storage/products/' . basename($image));
}

}
