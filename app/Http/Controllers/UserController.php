<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserInfoRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function allUsers()
    {
        if (auth()->user()->hasRole('admin')) {
            return response()->json([
                'data' => User::all(),
            ]);
        } else {
            return response()->json([
                'message' => 'you don\'t have the permission for this action',
            ]);
        }
    }
    public function getPosts()
    {
        $posts = auth()->user()->posts;
        $posts = $posts->map(function ($post) {
            $post['likes'] = $post->likers()->count();
            $post['comments'] = $post->comments()->count();
            return $post;
        });
        return response()->json([
            'data' => $posts,
        ]);
    }
    public function getLikedPosts()
    {
        $posts = auth()->user()->likedPosts;
        $posts = $posts->map(function ($post) {
            $post['likes'] = $post->likers()->count();
            $post['comments'] = $post->comments()->count();
            return $post;
        });
        return response()->json([
            'data' => $posts,
        ]);
    }
    public function getComments()
    {
        $comments = auth()->user()->comments;
        return response()->json([
            'data' => $comments,
        ]);
    }
    public function getUserInfo()
    {
        $user=auth()->user();
        $user['isAdmin'] = $user->hasRole('admin');
        return response()->json([
            'user' => $user,
            'postsNum' => $user->posts()->count(),
            'commentsNum' => $user->comments()->count(),
            'likesNum' => $user->likedPosts()->count(),
        ]);
    }
    public function getUserPostsById(User $user)
    {
        $posts = $user->posts;
        $posts = $posts->map(function ($post) {
            $post['is_liked'] = auth()->user()->likedPosts()->where('post_id', $post->id)->exists();
            $post['likes'] = $post->likers()->count();
            $post['comments'] = $post->comments()->count();
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
        $user->save();
        $user['isAdmin'] = $user->hasRole('admin');
        return response()->json([
            'message' => 'user information updated successfully',
            'data' => $user,
        ]);
    }
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = User::find(auth()->id());
        if (Hash::check($request->current_password,$user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            $user['isAdmin'] = $user->hasRole('admin');
            return response()->json([
                'message' => 'user Password updated successfully',
                'data' => $user,
            ]);
        }else{
            return response()->json([
                'message' => 'the current password that you give is not correct',
            ],401);
        }
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
