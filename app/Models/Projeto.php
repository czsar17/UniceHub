<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ComentarioProjeto;

class Projeto extends Model
{
    use HasFactory;

    protected $table = 'projetos';

    protected $fillable = [
        'user_id',
        'nome',
        'descricao',
        'categoria',
        'tecnologias',
        'repo_url',
        'capa',
        'status',
    ];

    protected $casts = [
        'tecnologias' => 'array',
    ];

    public function criador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function membros()
    {
        return $this->belongsToMany(User::class, 'projeto_user')
            ->withTimestamps();
    }
    public function curtidas()
    {
        return $this->belongsToMany(
            User::class,
            'projeto_curtidas'
        );
    }

    public function comentarios()
    {
        return $this->hasMany(
            ComentarioProjeto::class
        );
    }
}