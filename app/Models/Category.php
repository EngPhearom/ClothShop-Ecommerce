<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['id', 'name', 'slug', 'image', 'parent_id'];

    public function product(){
        return $this->hasMany(Product::class);
    }
}
