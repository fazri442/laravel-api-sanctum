<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostduaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->get();
        $res = [
            'success' => true,
            'data' => $posts,
            'message' => 'List posts',
        ];
        return response()->json($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts',
            'content' => 'required|string|max:255',
            'status' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $post = new Post;
        $post->title = $request->title;
        $post->slug = Str::slug($request->title, '-');
        $post->content = $request->content;
        $post->status = $request->status;
        if ($request->hasFile('image')){
            $path = $request->file('image')->store('posts', 'public');
            $post->image = $path;
        }
        $post->save();

        $res = [
            'success' => true,
            'data' => $post,
            'message' => 'Store Post'
        ];
        return response()->json($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (! $post){
            return response()->json([
                'message' => 'Data Not Found',
            ], 401);
        }
        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Show Post Detail'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts,id,'.$id,
            'content' => 'required|string|max:255',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $post = Post::find($id);
        $post->title = $request->title;
        $post->slug = Str::slug($request->title, '-');
        $post->content = $request->content;
        $post->status = $request->status;
        if ($request->hasFile('image')){
            if ($post->image && Storage::disk('public')->exists($post->image)){
                Storage::disk('public')->delete($post->image);
            
            $path = $request->file('image')->store('posts', 'public');
            $post->image = $path;
            }
        }
        $post->save();

        $res = [
            'success' => true,
            'data' => $post,
            'message' => 'Store Post'
        ];
        return response()->json($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message'=>'Data Not Found'], 404);
        }
        if ($post->image && Storage::disk('public')->exists($post->image)){
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        return response()->json([
            'data' => [],
            'message' => 'Post deleted successfully',
            'success' => true
        ]);
    }
}
