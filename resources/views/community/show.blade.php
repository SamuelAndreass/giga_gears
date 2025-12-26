@extends('layouts.main')
@section('title', $post->title)

@section('header')
    <style>
        .header-wrapper {
            width: 100%;
            height: 90px;
            padding-top: 20px; 
            background: #FFFFFF;
            border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px;
            max-width: 90%; 
            margin: 0 auto; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-hero {
            background: linear-gradient(87.6deg, #ffffff -10.06%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.69) 95.43%);
            color: #000;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .post-content {
            white-space: pre-wrap;
            line-height: 1.8;
        }
        .comment-card {
            border-left: 3px solid #e5e7eb;
            padding-left: 1rem;
            margin-left: 0;
        }
        .reply-card {
            margin-left: 2rem;
            border-left-color: #d1d5db;
        }
        .btn-gradient-blue {
            background: linear-gradient(135deg, #067CC2, #4EDAFE);
            color: white;
            border: none;
        }
        .btn-gradient-blue:hover {
            background: linear-gradient(135deg, #056ba8, #3ec8eb);
            color: white;
        }
    </style>
    
    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    @auth
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">My Order</a>
                    @endauth
                    <a href="{{ route('community.index') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Seminars</a>
                    @auth
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none text-dark" style="white-space: nowrap;">
                        <i class="bi bi-cart3"></i>
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->first()?->items()->sum('qty') ?? 0;
                        @endphp
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-6">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    @endauth
                </div>
            </div>

            @auth
            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center justify-content-center profile-btn" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center" style="gap: 9px;">
                    <span>Profile</span>
                    <img src="{{ asset(Auth::user()->customerProfile?->avatar_path ?? 'images/logo foto profile.png') }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%;">
                </div>
            </a>
            @else
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <span>Login</span>
            </a>
            @endauth
        </div>
    </div>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
    <a href="{{ route('community.index') }}" class="btn btn-outline-secondary rounded-pill mb-4">
        <i class="bi bi-arrow-left me-2"></i>Back to Communities
    </a>

    <div id="postContainer">
        <div class="post-hero">
            <div class="d-flex align-items-center gap-3 mb-4">
                <img src="{{ $post->author_avatar }}" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $post->author_name }}</h6>
                    <small class="text-muted">{{ $post->time_ago }}</small>
                </div>
            </div>
            <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 36px; margin-bottom: 15px;">{{ $post->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-4 text-muted">
                <span><i class="bi bi-eye me-1"></i><span id="viewsCount">{{ number_format($post->views) }}</span> Views</span>
                <span id="likesDisplay"><i class="bi bi-heart me-1"></i><span id="likesCount">{{ $post->likes_count }}</span> Likes</span>
                <span><i class="bi bi-chat me-1"></i><span id="commentsCount">{{ $post->comments_count }}</span> Comments</span>
            </div>
            @if($post->tags->count() > 0)
            <div class="mt-3">
                @foreach($post->tags as $tag)
                <span class="badge rounded-pill bg-white text-dark px-3 py-2 me-2">{{ $tag->name }}</span>
                @endforeach
            </div>
            @endif
        </div>
        
        @if($post->image_url)
        <img src="{{ $post->image_url }}" class="img-fluid rounded-4 mb-4 w-100" style="max-height: 400px; object-fit: cover;">
        @endif
        
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="post-content fs-5 text-secondary" style="font-family: 'Montserrat', sans-serif;">{!! nl2br(e($post->content)) !!}</div>
        </div>
        
        <div class="d-flex gap-3">
            <button class="btn btn-outline-danger rounded-pill px-4" onclick="toggleLike()">
                <i class="bi bi-heart-fill me-2"></i><span id="likeButtonText">Like</span>
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4" onclick="document.getElementById('commentContent').focus()">
                <i class="bi bi-chat me-2"></i>Comment
            </button>
            <button class="btn btn-outline-secondary rounded-pill px-4" onclick="sharePost()">
                <i class="bi bi-share me-2"></i>Share
            </button>
        </div>
    </div>

    <hr class="my-5">

    <div id="commentsSection">
        <h3 class="fw-bold mb-4" style="font-family: 'Chakra Petch', sans-serif;"><i class="bi bi-chat-dots me-2"></i>Comments</h3>
        <div id="commentsContainer">
            <div class="text-center py-3">
                <div class="spinner-border text-secondary spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <div class="card border-0 bg-light rounded-4 p-4 mt-4">
            <h5 class="fw-bold mb-3" style="font-family: 'Chakra Petch', sans-serif;">Leave a Comment</h5>
            <form id="commentForm">
                <div class="mb-3">
                    <textarea id="commentContent" class="form-control rounded-3" rows="3" placeholder="Write your comment here..."></textarea>
                </div>
                <button type="button" class="btn btn-gradient-blue rounded-pill px-4" onclick="submitComment()">
                    <i class="bi bi-send me-2"></i>Submit Comment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer')
<footer class="text-center py-4" style="background:linear-gradient(87.6deg,#FFFFFF 8.86%,rgba(78,218,254,0.67) 32.51%,rgba(6,124,194,0.93) 95.43%);">
    <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="220">
    <p class="mt-2" style="font-family:'Chakra Petch',sans-serif;font-style:italic;">Empowering your digital lifestyle with the best tech and software.</p>
    <p style="font-weight:bold;">&copy; {{ date('Y') }} GigaGears. All Rights Reserved.</p>
</footer>
@endsection

@section('footer-script')
<script>
    const postId = {{ $post->id }};
    const baseUrl = '/api/community';
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    let replyingToCommentId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadComments();
    });

    function formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    async function loadComments() {
        const container = document.getElementById('commentsContainer');
        
        try {
            const response = await fetch(`${baseUrl}/posts/${postId}/comments`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                container.innerHTML = result.data.map(comment => createCommentCard(comment)).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 2rem;"></i>
                        <p class="mt-2">No comments yet. Be the first to comment!</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            container.innerHTML = `
                <div class="alert alert-warning">Error loading comments.</div>
            `;
        }
    }

    function createCommentCard(comment, isReply = false) {
        let repliesHtml = '';
        if (comment.replies && comment.replies.length > 0) {
            repliesHtml = comment.replies.map(reply => createCommentCard(reply, true)).join('');
        }
        
        return `
            <div class="comment-card ${isReply ? 'reply-card' : ''} mb-3 py-3">
                <div class="d-flex gap-3">
                    <img src="${comment.author_avatar}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold">${comment.author_name}</span>
                            <small class="text-muted">${comment.time_ago}</small>
                        </div>
                        <p class="mb-2" style="font-family: 'Montserrat', sans-serif;">${comment.content}</p>
                        <button class="btn btn-link btn-sm text-muted p-0" onclick="replyToComment(${comment.id}, '${comment.author_name}')">
                            <i class="bi bi-reply me-1"></i>Reply
                        </button>
                    </div>
                </div>
                ${repliesHtml}
            </div>
        `;
    }

    async function toggleLike() {
        if (!isLoggedIn) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('likesCount').textContent = formatNumber(result.data.likes_count);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    }

    function replyToComment(commentId, authorName) {
        if (!isLoggedIn) {
            window.location.href = '{{ route("login") }}';
            return;
        }
        
        replyingToCommentId = commentId;
        const textarea = document.getElementById('commentContent');
        textarea.placeholder = `Replying to ${authorName}...`;
        textarea.focus();
    }

    async function submitComment() {
        if (!isLoggedIn) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        const content = document.getElementById('commentContent').value.trim();
        if (!content) {
            alert('Please enter a comment');
            return;
        }

        try {
            const data = {
                post_id: postId,
                content: content
            };
            
            if (replyingToCommentId) {
                data.parent_id = replyingToCommentId;
            }

            const response = await fetch(`${baseUrl}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('commentContent').value = '';
                document.getElementById('commentContent').placeholder = 'Write your comment here...';
                replyingToCommentId = null;
                loadComments();
                const count = parseInt(document.getElementById('commentsCount').textContent) + 1;
                document.getElementById('commentsCount').textContent = count;
            } else {
                alert('Error posting comment: ' + result.message);
            }
        } catch (error) {
            console.error('Error posting comment:', error);
            alert('Error posting comment. Please try again.');
        }
    }

    function sharePost() {
        if (navigator.share) {
            navigator.share({
                title: '{{ addslashes($post->title) }}',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert('Link copied to clipboard!');
        }
    }
</script>
@endsection
