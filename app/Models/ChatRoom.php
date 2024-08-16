<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $appends = ['channel_name'];


    // add channel name to the model
    public function getChannelNameAttribute()
    {
        return 'chat-room.' . $this->id;
    }

    // Define relationships if needed
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getMessagesAttribute()
    {
        return $this->messages()->get();
    }
}
