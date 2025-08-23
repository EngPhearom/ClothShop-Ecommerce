<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['id', 'name', 'slug', 'image'];

    public function product(){
        return $this->hasMany(Product::class);
    }
}
