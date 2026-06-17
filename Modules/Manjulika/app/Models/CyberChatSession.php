<?php

namespace Modules\Manjulika\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CyberChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'name',
        'browser_fingerprint',
        'ip_address',
        'user_agent',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(CyberChatMessage::class, 'chat_session_id')->orderBy('id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: 'Anonymous';
    }

    public function historyArray(): array
    {
        return $this->messages->map(fn ($m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->values()->all();
    }
}
