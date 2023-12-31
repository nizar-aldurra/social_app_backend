<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    protected $table = 'post_images';
    protected $fillable = ['path'];
    public function post(){
        return $this->belongsTo(Post::class,'post_id','id');
    }
}
