<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function get(Comment $comment){
        return response()->json([
            'data' => $comment,
        ]);
    }
    public function getOwner(Comment $comment){
        return response()->json([
            'data' => $comment->user,
        ]);
    }
    public function getPost(Comment $comment){
        return response()->json([
            'data' => $comment->post,
        ]);
    }
    public function all(){
        $comments=Comment::all();
        $comments=$comments->where('user_id','!=',auth()->id());
        $comments = collect($comments->values());
        return response()->json([
            'data' => $comments,
        ]);
    }
    public function create(AddCommentRequest $request){
        $validated=$request->validated();
        $validated['user_id']=auth()->user()->getAuthIdentifier();
        Comment::create($validated);
        return response()->json([
            'message' => 'comment created successfully',
            'created' => true,
        ],200);
    }
    public function update(UpdateCommentRequest $request,Comment $comment){
        if ($comment->user_id == auth()->id() || auth()->user()->hasRole('admin')) {
            $comment->body= $request->body;
            $comment->image = $request->image;
            $comment->save();
            return response()->json([
                'message' => 'comment updated successfully',
                'data' => $comment,
            ]);
        }
        else return response()->json([
            'message' => 'it\'s not allowed to update others comment'
        ]);


    }
    public function delete(Comment $comment){
        if ($comment->user_id == auth()->id() || auth()->user()->hasRole('admin')) {
            $comment->delete();
            return response()->json([
                'message' => 'comment deleted successfully',
            ]);
        }
        else return response()->json([
            'message' => 'it\'s not allowed to delete others comment'
        ]);

    }
}
