<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogListResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Blog::class);

        $query = Blog::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')));

        return BlogListResource::collection(
            QueryBuilder::for($query)
                ->allowedSorts('title', 'created_at', 'updated_at')
                ->defaultSort('-created_at')
                ->paginate(5)
        );
    }

    public function store(StoreBlogRequest $request): BlogListResource
    {
        $data = $request->validated();
        $data['slug'] = Blog::uniqueSlug($data['slug'] ?? $data['title']);

        return new BlogListResource(Blog::create($data));
    }

    public function show(Blog $blog): BlogListResource
    {
        $this->authorize('view', $blog);

        return new BlogListResource($blog);
    }

    public function update(UpdateBlogRequest $request, Blog $blog): BlogListResource
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data)) {
            $data['slug'] = Blog::uniqueSlug($data['slug'] ?: ($data['title'] ?? $blog->title), $blog->id);
        }

        $blog->update($data);

        return new BlogListResource($blog);
    }

    public function destroy(Blog $blog): Response
    {
        $this->authorize('delete', $blog);

        $blog->delete();

        return response()->noContent();
    }
}
