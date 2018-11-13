<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $fillable = [
        "user_id",
        "message_content",
        "message_type",
        "delivered_at",
        "read_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select('id','name');
    }
}
