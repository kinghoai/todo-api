<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = [
        'name',
        'completed'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
