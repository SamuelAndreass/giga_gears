@extends('layouts.main')
@section('title', 'Communities')

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
        .hero-gradient {
            background: linear-gradient(87.6deg, #ffffff -10.06%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.69) 95.43%);
            padding: 60px 0;
            margin-top: 20px;
        }
        .post-card {
            background: #fff;
            border: 1px solid #E5E5E5;
            border-radius: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .tag-filter.active {
            background-color: #067CC2 !important;
            color: white !important;
            border-color: #067CC2 !important;
        }
        .sort-btn.active {
            background-color: #067CC2 !important;
            color: white !important;
            border-color: #067CC2 !important;
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
        .cursor-pointer {
            cursor: pointer;
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

    <div class="hero-gradient">
        <div class="page-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 30px;">
                <div>
                    <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 46px; color: #fff; margin-bottom: 10px;">GigaGears Customer Communities</h1>
                    <p style="font-family: 'Montserrat', sans-serif; font-size: 18px; color: #fff; opacity: 0.9;">Join the conversation! Share your experiences, ask questions, and connect with fellow tech enthusiasts.</p>
                </div>
                <div>
                    <div class="input-group bg-white rounded-pill shadow-sm" style="width: 300px;">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-0 bg-transparent" placeholder="Search posts...">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
    <div class="row gy-4">
        <div class="col-lg-3 order-lg-2">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-4" style="font-family: 'Chakra Petch', sans-serif;"><i class="bi bi-tag me-2"></i>Popular Tags</h5>
                <div id="tagsContainer" class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <button class="btn btn-outline-secondary btn-sm rounded-pill tag-filter" data-tag="{{ $tag->name }}">
                        {{ $tag->name }}
                    </button>
                    @endforeach
                </div>
                <hr class="my-4">
                <button class="btn btn-outline-secondary btn-sm rounded-pill w-100 clear-filter" style="display: none;">
                    <i class="bi bi-x me-1"></i>Clear Filter
                </button>
            </div>
        </div>
        
        <div class="col-lg-9 order-lg-1">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center gap-3">
                    @auth
                    <img src="{{ asset(Auth::user()->customerProfile?->avatar_path ?? 'images/logo foto profile.png') }}" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                    @else
                    <img src="{{ asset('images/logo foto profile.png') }}" class="rounded-circle" width="48" height="48">
                    @endauth
                    <button class="form-control text-start text-muted bg-light rounded-pill cursor-pointer" data-bs-toggle="modal" data-bs-target="#createPostModal" style="cursor: pointer;">
                        What's on your mind? Start a new discussion...
                    </button>
                    <button class="btn btn-gradient-blue rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#createPostModal">
                        <i class="bi bi-plus-circle me-2"></i>Create Post
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0" style="font-family: 'Chakra Petch', sans-serif;">Latest Posts</h4>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm sort-btn active" data-sort="newest">Newest</button>
                    <button class="btn btn-outline-secondary btn-sm sort-btn" data-sort="popular">Popular</button>
                    <button class="btn btn-outline-secondary btn-sm sort-btn" data-sort="most_liked">Most Liked</button>
                </div>
            </div>

            <div id="postsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading posts...</p>
                </div>
            </div>

            <div id="loadMoreContainer" class="text-center mt-4" style="display: none;">
                <button id="loadMoreBtn" class="btn btn-outline-primary rounded-pill px-5">
                    <i class="bi bi-arrow-down-circle me-2"></i>Load More Posts
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="font-family: 'Chakra Petch', sans-serif;">Create New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="createPostForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" id="postTitle" class="form-control form-control-lg rounded-3" placeholder="Enter a catchy title..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content</label>
                        <textarea id="postContent" class="form-control rounded-3" rows="5" placeholder="Share your thoughts, tips, or questions..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image URL (optional)</label>
                        <input type="url" id="postImageUrl" class="form-control rounded-3" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tags</label>
                        <div id="tagCheckboxes" class="d-flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $tag->id }}" id="tag{{ $tag->id }}">
                                <label class="form-check-label" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gradient-blue rounded-pill px-5 fw-semibold" onclick="createPost()">
                    <i class="bi bi-send me-2"></i>Publish Post
                </button>
            </div>
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
    const baseUrl = '/api/community';
    let currentPage = 1;
    let currentSort = 'newest';
    let currentTag = '';
    let currentSearch = '';
    let lastPage = 1;
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

    document.addEventListener('DOMContentLoaded', function() {
        loadPosts();
        
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentSort = this.dataset.sort;
                currentPage = 1;
                loadPosts();
            });
        });

        document.querySelectorAll('.tag-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tag-filter').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentTag = this.dataset.tag;
                currentPage = 1;
                document.querySelector('.clear-filter').style.display = 'block';
                loadPosts();
            });
        });

        document.querySelector('.clear-filter')?.addEventListener('click', function() {
            document.querySelectorAll('.tag-filter').forEach(b => b.classList.remove('active'));
            currentTag = '';
            currentPage = 1;
            this.style.display = 'none';
            loadPosts();
        });

        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value.trim();
                currentPage = 1;
                loadPosts();
            }, 300);
        });

        document.getElementById('loadMoreBtn')?.addEventListener('click', function() {
            if (currentPage < lastPage) {
                currentPage++;
                loadPosts(true);
            }
        });
    });

    async function loadPosts(append = false) {
        const container = document.getElementById('postsContainer');
        
        if (!append) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading posts...</p>
                </div>
            `;
        }

        const params = new URLSearchParams({
            page: currentPage,
            sort: currentSort,
        });
        if (currentTag) params.append('tag', currentTag);
        if (currentSearch) params.append('search', currentSearch);

        try {
            const response = await fetch(`${baseUrl}/posts?${params}`);
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                const postsHtml = result.data.map(post => createPostCard(post)).join('');
                
                if (append) {
                    container.insertAdjacentHTML('beforeend', postsHtml);
                } else {
                    container.innerHTML = postsHtml;
                }

                lastPage = result.pagination.last_page;
                document.getElementById('loadMoreContainer').style.display = 
                    currentPage < lastPage ? 'block' : 'none';
            } else if (!append) {
                container.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No posts yet</h5>
                        <p>Be the first to start a discussion!</p>
                    </div>
                `;
                document.getElementById('loadMoreContainer').style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading posts:', error);
            if (!append) {
                container.innerHTML = `
                    <div class="alert alert-danger">Error loading posts. Please try again later.</div>
                `;
            }
        }
    }

    function createPostCard(post) {
        const tags = post.tags && post.tags.length > 0 
            ? post.tags.map(t => `<span class="badge bg-secondary rounded-pill me-1">${t.name}</span>`).join('') 
            : '';
        
        return `
            <div class="card post-card border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="${post.author_avatar}" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold">${post.author_name}</h6>
                            <small class="text-muted">${post.time_ago}</small>
                        </div>
                    </div>
                    <a href="/community/${post.id}" class="text-decoration-none text-dark">
                        <h5 class="card-title fw-bold mb-3" style="font-family: 'Chakra Petch', sans-serif;">${post.title}</h5>
                    </a>
                    <p class="card-text text-secondary mb-3" style="font-family: 'Montserrat', sans-serif;">${post.content.substring(0, 200)}${post.content.length > 200 ? '...' : ''}</p>
                    ${post.image_url ? `<img src="${post.image_url}" class="img-fluid rounded-3 mb-3" style="max-height: 300px; object-fit: cover; width: 100%;">` : ''}
                    <div class="d-flex flex-wrap gap-2 mb-3">${tags}</div>
                    <div class="d-flex align-items-center gap-4 text-muted">
                        <span class="cursor-pointer" onclick="toggleLike(${post.id}, this)" style="cursor: pointer;">
                            <i class="bi bi-heart${post.user_liked ? '-fill text-danger' : ''} me-1"></i>
                            <span class="like-count">${formatNumber(post.likes_count)}</span>
                        </span>
                        <a href="/community/${post.id}" class="text-muted text-decoration-none">
                            <i class="bi bi-chat me-1"></i>${post.comments_count}
                        </a>
                        <span><i class="bi bi-eye me-1"></i>${formatNumber(post.views)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    async function toggleLike(postId, element) {
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
                const icon = element.querySelector('i');
                const count = element.querySelector('.like-count');
                
                if (result.data.liked) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill', 'text-danger');
                } else {
                    icon.classList.remove('bi-heart-fill', 'text-danger');
                    icon.classList.add('bi-heart');
                }
                count.textContent = formatNumber(result.data.likes_count);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    }

    async function createPost() {
        if (!isLoggedIn) {
            window.location.href = '{{ route("login") }}';
            return;
        }

        const title = document.getElementById('postTitle').value.trim();
        const content = document.getElementById('postContent').value.trim();
        const imageUrl = document.getElementById('postImageUrl').value.trim();
        const tagCheckboxes = document.querySelectorAll('#tagCheckboxes input:checked');
        const tags = Array.from(tagCheckboxes).map(cb => cb.value);

        if (!title || !content) {
            alert('Please fill in the title and content');
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/posts`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    title,
                    content,
                    image_url: imageUrl || null,
                    tags
                })
            });
            const result = await response.json();

            if (result.success) {
                document.getElementById('postTitle').value = '';
                document.getElementById('postContent').value = '';
                document.getElementById('postImageUrl').value = '';
                document.querySelectorAll('#tagCheckboxes input').forEach(cb => cb.checked = false);
                bootstrap.Modal.getInstance(document.getElementById('createPostModal')).hide();
                currentPage = 1;
                loadPosts();
            } else {
                alert('Error creating post: ' + result.message);
            }
        } catch (error) {
            console.error('Error creating post:', error);
            alert('Error creating post. Please try again.');
        }
    }
</script>
@endsection
