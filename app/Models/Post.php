<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title','body','image','user_id','user_name',];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function likers(){
        return $this->belongsToMany(User::class,'user_post_likes','post_id','user_id');
    }
    public static function boot() {
        parent::boot();
        self::deleting(function($post) { 
            $post->comments()->each(function($comment) {
               $comment->delete();
             });
             $post->likers()->detach();
        });
    }
    public function images() {
        return $this->hasMany(Image::class,'post_id','id');
    }
}
