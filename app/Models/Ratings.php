<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;
    protected $table = 'product_ratings';
    protected $fillable = [
        'product_id',
        'rate',
        'count'
      ];
}
