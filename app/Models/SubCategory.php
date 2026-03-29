<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table = "sub_category";
    // 🔗 Belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
