<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conversation extends Model
{
    use HasUuids;

    protected $table = 'conversations';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'owner_key', 'title', 'ip', 'user_agent',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
