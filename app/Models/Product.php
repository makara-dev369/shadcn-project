<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'title',
        'description',
        'price',
        'brand_ref_id',
        'category_ref_id',
        "discount_price",
        "benefits_content",
        "ingredients_content",
        "howtouse_content",
        "product_size_id",
        'is_active'
    ];

    public function image(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attechImages(array $image): void
    {
        foreach ($image as $img) {
            $this->image()->create([
                'url' => $img
            ]);
        }
    }


    public function syncImages(array $image): void
    {
        $this->image()->delete();
        $this->attechImages($image);
    }
}
