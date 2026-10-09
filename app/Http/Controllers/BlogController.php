<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()->orderByDesc('published_at')->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $post)
    {
        if ($post->status !== 'published' || ($post->published_at && $post->published_at->isFuture())) {
            abort(404);
        }

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
