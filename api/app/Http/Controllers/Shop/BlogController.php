<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCommentResource;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Support\Enums\ModerationStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        return BlogPostResource::collection(
            BlogPost::published()
                ->with('author')
                ->when($request->input('search'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
                ->latest('published_at')
                ->paginate(9)
        );
    }

    public function show(string $slug): BlogPostResource
    {
        return new BlogPostResource(BlogPost::published()->where('slug', $slug)->with('author')->firstOrFail());
    }

    public function comments(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        return BlogCommentResource::collection(
            $post->comments()->where('status', ModerationStatus::Approved->value)->with('user')->latest()->paginate(20)
        );
    }

    public function storeComment(Request $request, string $slug): JsonResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $data = $request->validate(['content' => ['required', 'string', 'min:3', 'max:2000']]);

        $comment = $post->comments()->make([...$data, 'status' => ModerationStatus::Pending]);
        $comment->user()->associate($request->user())->save();

        return (new BlogCommentResource($comment->load('user')))->response()->setStatusCode(201);
    }
}
