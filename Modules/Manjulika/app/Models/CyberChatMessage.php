<?php

namespace Modules\Manjulika\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CyberChatMessage extends Model
{
    protected $fillable = ['chat_session_id', 'role', 'content'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CyberChatSession::class, 'chat_session_id');
    }
}
