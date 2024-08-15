<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Define relationships if needed
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
