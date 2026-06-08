<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private function serialize($post) {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'text' => $post->text,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'email' => $post->user->email,
            ],
            'created_at' => $post->created_at->toISOString(),
            'updated_at' => $post->updated_at->toISOString(),
        ];
    }

    public function index(Request $request) {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');

        $query = Post::with('user');

        if ($sortBy === 'title') {
            $query->orderBy('title', $order);
        } else {
            $query->orderBy('created_at', $order);
        }

        $total = $query->count();
        $posts = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'data' => $posts->map(fn($post) => $this->serialize($post)),
            'meta' => ['total' => $total, 'offset' => $offset, 'limit' => $limit]
        ]);
    }

    public function myPosts(Request $request) {
        $user = $request->user();
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');

        $query = $user->posts();

        if ($sortBy === 'title') {
            $query->orderBy('title', $order);
        } else {
            $query->orderBy('created_at', $order);
        }

        $total = $query->count();
        $posts = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'data' => $posts->map(fn($post) => $this->serialize($post)),
            'meta' => ['total' => $total, 'offset' => $offset, 'limit' => $limit]
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json($this->serialize($post), 201);
    }
}
