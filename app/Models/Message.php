<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_room_id', 'user_id', 'content'];

    protected $appends = ['user_name'];

    // protected $hidden = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user->name;
    }
}
