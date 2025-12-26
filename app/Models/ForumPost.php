<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_posts';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_url',
        'views',
    ];

    protected $casts = [
        'views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ForumLike::class, 'post_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ForumTag::class, 'forum_post_tags', 'post_id', 'tag_id');
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown';
    }

    public function getAuthorAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->author_name) . '&background=1F2937&color=fff';
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }
}
