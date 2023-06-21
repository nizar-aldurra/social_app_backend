<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserInfoRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function allUsers(){
        if(auth()->user()->hasRole('admin')){
            return response()->json([
                'data' => User::all(),
            ]);
        }
        else{
            return response()->json([
                'message' => 'you don\'t have the permission for this action',
            ]);
        }
    }
    public function getPosts()
    {
        return response()->json([
            'data' => auth()->user()->posts,
        ]);
    }
    public function getActivities()
    {
    $posts=auth()->user()->posts;
    $comments = auth()->user()->comments;
    $posts_comment = $comments->pluck('post');
    $merged = $posts->merge($posts_comment);
        return response()->json([
            'merged' => $merged,
            'posts' => $posts,
            'comment_posts' => $posts_comment,
        ]);
    }
    public function getUserPostsById(User $user){
        $posts = $user->posts;
        $posts= $posts->map(function ($post){
            $post['is_liked'] = auth()->user()->likedPosts()->where('post_id',$post->id)->exists();
            return $post;
        });
        $user['isAdmin'] = $user->hasRole('admin');
        return response()->json([
            'data' => $posts,
            'user' => $user,
        ]);
    }
    public function updateInfo(UpdateUserInfoRequest $request)
    {
        $user = User::find(auth()->id());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->birth_day = $request->birth_day;
        $user->save();
        $user1=User::find(auth()->id());
        return response()->json([
            'message' => 'user information updated successfully',
            'data' => $user,
            'user1' => $user1
        ]);
    }
    public function updateUserById(UpdateUserInfoRequest $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->birth_day = $request->birth_day;
        $user->save();
        return response()->json([
            'message' => 'user information updated successfully',
            'data' => $user
        ]);
    }
    public function deleteUserById(User $user)
    {
        if (auth()->user()->hasRole('admin')) {
            $user->delete();
            return response()->json([
                'message' => 'user deleted successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'you don\'t have the permission to delete user.'
            ]);
        }
    }
}