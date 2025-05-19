<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with('commentable');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('author_email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('commentable_type')) {
            $query->where('commentable_type', $request->input('commentable_type'));
        }

        if ($request->has('commentable_id')) {
            $query->where('commentable_id', $request->input('commentable_id'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $comments = $query->paginate($perPage);

        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentStoreRequest $request)
    {
        $data = $request->validated();
        
        // Set author to current user if authenticated
        if (auth()->check() && !isset($data['author_id'])) {
            $data['author_id'] = auth()->id();
        }

        $comment = Comment::create($data);
        $comment->load('commentable');

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        $comment->load('commentable');
        
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        $data = $request->validated();

        $comment->update($data);
        $comment->load('commentable');

        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Approve the specified comment.
     */
    public function approve(Comment $comment)
    {
        $this->authorize('update', $comment);
        
        $comment->update(['status' => 'approved']);
        $comment->load('commentable');

        return response()->json($comment);
    }

    /**
     * Reject the specified comment.
     */
    public function reject(Comment $comment)
    {
        $this->authorize('update', $comment);
        
        $comment->update(['status' => 'rejected']);
        $comment->load('commentable');

        return response()->json($comment);
    }
}
