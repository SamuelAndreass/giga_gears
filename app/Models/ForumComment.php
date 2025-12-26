<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumComment extends Model
{
    use HasFactory;

    protected $table = 'forum_comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'parent_id');
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
}
