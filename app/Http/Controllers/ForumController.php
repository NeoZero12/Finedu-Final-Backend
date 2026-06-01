<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        return response()->json(
            ForumPost::with(['user:id,name', 'comments.user:id,name'])
                ->withCount('comments')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $post = ForumPost::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return response()->json([
            'message' => 'Topik forum berhasil dibuat.',
            'data' => $post->load('user:id,name'),
        ], 201);
    }

    public function comment(Request $request, ForumPost $post)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment = ForumComment::create([
            'forum_post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan.',
            'data' => $comment->load('user:id,name'),
        ], 201);
    }

    public function destroyPost(ForumPost $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Topik forum berhasil dihapus.',
        ]);
    }

    public function destroyComment(ForumComment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Komentar berhasil dihapus.',
        ]);
    }
}
