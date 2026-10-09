<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'draft' => BlogPost::where('status', 'draft')->count(),
        ];

        return view('admin.blog.index', compact('posts', 'stats'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug|alpha_dash',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'meta_description' => 'required|string|max:300',
            'author_name' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $post)
    {
        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'meta_description' => 'required|string|max:300',
            'author_name' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $post->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted.');
    }

    public function togglePublish(BlogPost $post)
    {
        if ($post->status === 'published') {
            $post->update(['status' => 'draft', 'published_at' => null]);
        } else {
            $post->update(['status' => 'published', 'published_at' => now()]);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Status updated.');
    }
}
