<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id')->select('id','category_name');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id')->select('id','name');
    }
}
