<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_room_id', 'user_id', 'content','archived'];

    protected $appends = ['user_name'];

    protected $hidden = ['user'];


    protected static function booted()
    {
        static::addGlobalScope('notArchived', function (Builder $builder) {
            $builder->where('archived', false);
        });
    }

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

    public function archive()
    {
        $this->archived = true;
        $this->save();
    }
}
