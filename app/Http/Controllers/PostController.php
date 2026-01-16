<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->paginate(20);

        return response()->json($posts);
    }

    public function show(Post $post)
    {
        if (is_null($post->published_at) || $post->published_at->isFuture()) {
            abort(404);
        }

        return response()->json($post->load('user'));
    }

    public function create()
    {
        return 'posts.create';
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Auth::user()->posts()->create($data);

        return response()->json($post, 201);
    }

    public function edit(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        return 'posts.edit';
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
