<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumLike;
use App\Models\ForumTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function index()
    {
        $tags = ForumTag::all();
        return view('community.index', compact('tags'));
    }

    public function show($id)
    {
        $post = ForumPost::with(['user', 'tags', 'comments.user', 'comments.replies.user'])
            ->findOrFail($id);
        
        $post->increment('views');
        
        return view('community.show', compact('post'));
    }

    public function getPosts(Request $request): JsonResponse
    {
        $query = ForumPost::with(['user', 'tags', 'likes'])
            ->withCount(['likes', 'comments']);
        
        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('content', 'ILIKE', "%{$search}%");
            });
        }
        
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'most_liked':
                $query->orderByDesc('likes_count');
                break;
            default:
                $query->latest();
        }
        
        $posts = $query->paginate(10);
        
        $data = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'image_url' => $post->image_url,
                'views' => $post->views,
                'author_name' => $post->author_name,
                'author_avatar' => $post->author_avatar,
                'time_ago' => $post->time_ago,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'tags' => $post->tags->map(fn($tag) => ['id' => $tag->id, 'name' => $tag->name]),
                'user_liked' => Auth::check() ? $post->isLikedBy(Auth::user()) : false,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
            ]
        ]);
    }

    public function getPost($id): JsonResponse
    {
        $post = ForumPost::with(['user', 'tags', 'likes'])
            ->withCount(['likes', 'comments'])
            ->find($id);
        
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }
        
        $post->increment('views');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'image_url' => $post->image_url,
                'views' => $post->views,
                'author_name' => $post->author_name,
                'author_avatar' => $post->author_avatar,
                'time_ago' => $post->time_ago,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'tags' => $post->tags->map(fn($tag) => ['id' => $tag->id, 'name' => $tag->name]),
                'user_liked' => Auth::check() ? $post->isLikedBy(Auth::user()) : false,
            ]
        ]);
    }

    public function storePost(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }
        
        $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'required|string',
            'image_url' => 'nullable|url|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:forum_tags,id',
        ]);
        
        $post = ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
        ]);
        
        if ($request->has('tags') && !empty($request->tags)) {
            $post->tags()->attach($request->tags);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => ['id' => $post->id]
        ]);
    }

    public function deletePost($id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }
        
        $post = ForumPost::find($id);
        
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }
        
        if ($post->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $post->delete();
        
        return response()->json(['success' => true, 'message' => 'Post deleted successfully']);
    }

    public function toggleLike($id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }
        
        $post = ForumPost::find($id);
        
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }
        
        $existingLike = ForumLike::where('post_id', $id)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            ForumLike::create([
                'post_id' => $id,
                'user_id' => Auth::id(),
            ]);
            $liked = true;
        }
        
        $likesCount = $post->likes()->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]
        ]);
    }

    public function getComments($postId): JsonResponse
    {
        $comments = ForumComment::with(['user', 'replies.user'])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $data = $comments->map(function($comment) {
            return $this->formatComment($comment);
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function formatComment($comment): array
    {
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'author_name' => $comment->author_name,
            'author_avatar' => $comment->author_avatar,
            'time_ago' => $comment->time_ago,
            'replies' => $comment->replies->map(fn($reply) => $this->formatComment($reply)),
        ];
    }

    public function storeComment(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }
        
        $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_comments,id',
        ]);
        
        $comment = ForumComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'author_name' => $comment->author_name,
                'author_avatar' => $comment->author_avatar,
                'time_ago' => $comment->time_ago,
            ]
        ]);
    }

    public function deleteComment($id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }
        
        $comment = ForumComment::find($id);
        
        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
        }
        
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $comment->delete();
        
        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    }

    public function getTags(): JsonResponse
    {
        $tags = ForumTag::withCount('posts')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tags->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'posts_count' => $tag->posts_count,
            ])
        ]);
    }
}
