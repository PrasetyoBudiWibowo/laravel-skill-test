<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PostController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $posts = Post::active()
            ->with('user')
            ->paginate(20);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        if (! $post->is_active) {
            abort(404);
        }

        return new PostResource($post->load('user'));
    }

    public function create()
    {
        return 'posts.create';
    }

    public function store(StorePostRequest $request)
    {
        $post = $request->user()->posts()->create($request->validated());

        return new PostResource($post);
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
