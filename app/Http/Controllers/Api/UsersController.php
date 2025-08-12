<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $post = User::latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $post,
            'message' => 'List posts',
        ], 200);
    }

//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'title'   => 'required|string|max:255|unique:posts,title',
//             'content' => 'required|string|max:255',
//             'status'  => 'required',
//             'foto'    => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
//         ]);

//         if ($validator->fails()) {
//             return response()->json($validator->errors(), 400);
//         }

//         $post = new Post();
//         $post->title   = $request->title;
//         $post->slug    = Str::slug($request->title, '-');
//         $post->content = $request->content;
//         $post->status  = $request->status;

//         if ($request->hasFile('foto')) {
//             $path = $request->file('foto')->store('post', 'public');
//             $post->foto = $path;
//         }

//         $post->save();

//         return response()->json([
//             'success' => true,
//             'data'    => $post,
//             'message' => 'Store posts',
//         ], 201);
//     }

//     public function show($id)
//     {
//         $post = Post::find($id);
//         if (!$post) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Data not found',
//             ], 404);
//         }

//         return response()->json([
//             'success' => true,                
//             'data'    => $post,
//             'message' => 'Show Post Detail',
//         ], 200);
//     }

//    public function update(Request $request, $id)
//     {
//         $validator = Validator::make($request->all(), [
//             'title' => 'required|string|max:255|unique:posts,id,' . $id,
//             'content' => 'required|string|max:255',
//             'status' => 'required',
//             'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'data' => [],
//                 'message' => $validator->errors(),
//                 'success' => false
//             ]);
//         }

//         $post = Post::find($id);
//         $post->title = $request->title;
//         $post->slug = Str::slug($request->title, '-');
//         $post->content = $request->content;
//         $post->status = $request->status;
//         if ($request->hasFile('foto')) {
//             if ($post->foto && Storage::disk('public')->exists($post->foto)) {
//                 Storage::disk('public')->delete($post->foto);
//             }
//             $path = $request->file('foto')->store('post', 'public');
//             $post->foto = $path;
//         }
//         $post->save();

//         $res = [
//             'success' => true,
//             'data' => $post,
//             'message' => 'Store Post'
//         ];
//         return response()->json($res, 200);
//     }

//     public function destroy($id)
//     {
//         $post = Post::find($id);
//         if (!$post) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Data not found'
//             ], 404);
//         }

//         if ($post->foto && Storage::disk('public')->exists($post->foto)) {
//             Storage::disk('public')->delete($post->foto);
//         }

//         $post->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Post deleted successfully',
//             'data'    => []
//         ], 200);
//     }
 }
