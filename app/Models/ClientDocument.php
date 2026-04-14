<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use HasFactory;

    protected $table = 'client_documents';

    protected $fillable = [
        'users_id',
        'document_name',
        'file_path',
        'file_type',
        'document_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}