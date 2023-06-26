<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function get(Post $post)
    {
        $post['is_liked'] = auth()->user()->likedPosts()->where('post_id', $post->id)->exists();
        $post['likes'] = $post->likers()->count();
        $post['comments'] = $post->comments()->count();
        return response()->json([
            'data' => $post,
        ]);
    }
    public function getOwner(Post $post)
    {
        return response()->json([
            'data' => $post->user,
        ]);
    }
    public function getComments(Post $post)
    {
        return response()->json([
            'data' => $post->comments,
        ]);
    }
    public function changeLikingStatus(Post $post)
    {
        if (auth()->user()->likedPosts()->where('post_id', $post->id)->exists()) {
            auth()->user()->likedPosts()->detach($post->id);
        } else {
            auth()->user()->likedPosts()->attach($post->id);
        }
        return response()->json([
            'isLiked' => auth()->user()->likedPosts()->where('post_id', $post->id)->exists()
        ]);
    }
    public function all()
    {
        $posts = Post::with('images')->get();
        $posts = $posts->where('user_id', '!=', auth()->id());
        $posts = collect($posts->values());
        $posts = $posts->map(function ($post) {
            $post['is_liked'] = auth()->user()->likedPosts()->where('post_id', $post->id)->exists();
            $post['likes'] = $post->likers()->count();
            $post['comments'] = $post->comments()->count();
            return $post;
        });
        return response()->json([
            'data' => $posts,
        ]);
    }
    public function create(AddPostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['user_name'] = auth()->user()->name;
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $extension = $image->getClientOriginalExtension();
                $imageName = 'image'.time().Str::random(100).'.'.$extension;
                $path = $image->storeAs('images',$imageName);
                $uploadedImages[] = $path;
            }
        }
        try {
        $post = Post::create($validated);
        $post->images()->createMany(
            array_map(function ($path) {
                return ['path' => $path];
            }, $uploadedImages)
        );
          } catch (\Exception $e) {
          
            error_log($e->getMessage());
          }

        return response()->json([
            'message' => 'post created successfully',
            'created' => true,
        ], 200);
    }
    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->user_id == auth()->id() || auth()->user()->hasRole('admin')) {
            $post->title = $request->title;
            $post->body = $request->body;
            $post->image = $request->image;
            $post->save();
            return response()->json([
                'message' => 'post updated successfully',
                'data' => $post,
            ]);
        } else
            return response()->json([
                'message' => 'it\'s not allowed to update others post'
            ]);
    }
    public function delete(Post $post)
    {
        if ($post->user_id == auth()->id() || auth()->user()->hasRole('admin')) {
            $post_id = $post->id;
            $post->delete();
            return response()->json([
                'message' => 'post deleted successfully',
                'post' => Post::find($post_id)
            ]);
        } else
            return response()->json([
                'message' => 'it\'s not allowed to delete others post'
            ]);
    }
}