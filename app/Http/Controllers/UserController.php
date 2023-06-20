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
    public function getUserPostsById(User $user){
        $posts = $user->posts;
        $posts= $posts->map(function ($post){
            $post['is_liked'] = auth()->user()->likedPosts()->where('post_id',$post->id)->exists();
            return $post;
        });
        return response()->json([
            'data' => $posts,
            'user' => $user,
        ]);
    }
    public function updateInfo(UpdateUserInfoRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->birth_day = $request->birth_day;
        $user->save;
        return response()->json([
            'message' => 'user information updated successfully',
            'data' => auth()->user()
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