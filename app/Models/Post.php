<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Post extends Model
{
    use HasFactory, AsSource, Attachable, Filterable;

    protected $fillable = [
        'title',
        'text',
        'user_id'
        ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
