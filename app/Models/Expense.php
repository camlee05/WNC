<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'spendings';
    protected $fillable = ['category_id', 'spend_date', 'amount', 'note'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

