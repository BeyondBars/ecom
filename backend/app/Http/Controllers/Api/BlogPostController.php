<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostStoreRequest;
use App\Http\Requests\BlogPostUpdateRequest;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(BlogPost::class, 'blog_post');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with('author');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('author_id')) {
            $query->where('author_id', $request->input('author_id'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $posts = $query->paginate($perPage);

        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogPostStoreRequest $request)
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        // Set author to current user if not provided
        if (!isset($data['author_id'])) {
            $data['author_id'] = auth()->id();
        }

        $post = BlogPost::create($data);
        $post->load('author');

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        $blogPost->load('author');
        
        return response()->json($blogPost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogPostUpdateRequest $request, BlogPost $blogPost)
    {
        $data = $request->validated();
        
        // Update slug if title changed and slug not provided
        if (isset($data['title']) && !isset($data['slug']) && $data['title'] !== $blogPost->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        $blogPost->update($data);
        $blogPost->load('author');

        return response()->json($blogPost);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        // Delete associated comments
        $blogPost->comments()->delete();
        
        // Delete blog post
        $blogPost->delete();

        return response()->json(null, 204);
    }
}
