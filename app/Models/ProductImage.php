<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'table_product_image';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'url',
    ];
}
