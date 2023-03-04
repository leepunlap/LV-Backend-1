<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSearch extends Model
{
    use HasFactory;

    public $fillable = ['search_id', 'user_id', 'params', 'ip', 'agent'];
}
