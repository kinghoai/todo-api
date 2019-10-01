<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = [
        'name',
        'completed',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function users() {
        return $this->belongsTo('App\User');
    }
}
