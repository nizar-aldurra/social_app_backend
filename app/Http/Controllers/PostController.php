<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function get(Post $post)
    {
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
    public function all()
    {
        $posts=Post::all();
        $posts=$posts->where('user_id','!=',auth()->id());
        return response()->json([
            'data' => $posts,
        ]);
    }
    public function create(AddPostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $post = Post::create($validated);
        return response()->json([
            'message' => 'post created successfully',
            'data' => $post,
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
        }
        else return response()->json([
            'message' => 'it\'s not allowed to update others post'
        ]);
    }
    public function delete(Post $post)
    {
        if ($post->user_id == auth()->id() || auth()->user()->hasRole('admin')) {
        $post->delete();
        return response()->json([
            'message' => 'post deleted successfully',
        ]);
        }
        else return response()->json([
            'message' => 'it\'s not allowed to delete others post'
        ]);
    }
}