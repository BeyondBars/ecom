<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BlogPostController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BlogPost::class);

        return BlogPostResource::collection(
            QueryBuilder::for(BlogPost::query()->with('author')->withCount('comments'))
                ->allowedFilters(AllowedFilter::exact('status'), AllowedFilter::partial('search', 'title'))
                ->allowedSorts('created_at', 'published_at', 'title')
                ->defaultSort('-created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', BlogPost::class);

        $post = new BlogPost($this->validated($request));
        $post->author()->associate($request->user())->save();

        return (new BlogPostResource($post->load('author')))->response()->setStatusCode(201);
    }

    public function show(BlogPost $blogPost): BlogPostResource
    {
        $this->authorize('view', $blogPost);

        return new BlogPostResource($blogPost->load('author')->loadCount('comments'));
    }

    public function update(Request $request, BlogPost $blogPost): BlogPostResource
    {
        $this->authorize('update', $blogPost);
        $blogPost->update($this->validated($request, $blogPost));

        return new BlogPostResource($blogPost->load('author'));
    }

    public function destroy(BlogPost $blogPost): Response
    {
        $this->authorize('delete', $blogPost);
        $blogPost->delete();

        return response()->noContent();
    }

    protected function validated(Request $request, ?BlogPost $post = null): array
    {
        $required = $post ? 'sometimes' : 'required';

        return $request->validate([
            'title' => [$required, 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')->ignore($post)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => [$required, 'string', 'max:100000'],
            'cover_image_url' => ['nullable', 'string', 'max:2048'],
            'status' => ['sometimes', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
