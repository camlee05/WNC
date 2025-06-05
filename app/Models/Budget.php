<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'month_year',
        'category_id',
        'target_amount',
        'note',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
