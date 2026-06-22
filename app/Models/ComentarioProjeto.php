<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioProjeto extends Model
{
    protected $fillable = [
        'comentario',
        'user_id',
        'projeto_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }
}
