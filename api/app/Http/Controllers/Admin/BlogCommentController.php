<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCommentResource;
use App\Models\BlogComment;
use App\Support\Enums\ModerationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BlogCommentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BlogComment::class);

        return BlogCommentResource::collection(
            QueryBuilder::for(BlogComment::query()->with('user', 'post'))
                ->allowedFilters(AllowedFilter::exact('status'), AllowedFilter::exact('blog_post_id'))
                ->defaultSort('-created_at')
                ->allowedSorts('created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function update(Request $request, BlogComment $comment): BlogCommentResource
    {
        $this->authorize('update', $comment);
        $comment->update($request->validate(['status' => ['required', Rule::enum(ModerationStatus::class)]]));

        return new BlogCommentResource($comment->load('user', 'post'));
    }

    public function destroy(BlogComment $comment): Response
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->noContent();
    }
}
