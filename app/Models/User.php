<?php

namespace App\Models;

use App\Models\Atividade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'data_nascimento',
        'tipo',
        'is_admin',
        'approval_status',
        'approval_requested_at',
        'approval_reviewed_at',
        'approval_reviewed_by',
        'foto',
        'curso',
        'telefone',
        'quantidade_projetos',
        'seguidores',
        'seguindo',
        'sobre_mim',
        'tecnologias',
        'interesses_markdown'
    ]; 

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function seguidores()
{
    return $this->belongsToMany(
        User::class,
        'followers',
        'seguido_id',
        'seguidor_id'
    )->withPivot('status');
}

public function seguindo()
{
    return $this->belongsToMany(
        User::class,
        'followers',
        'seguidor_id',
        'seguido_id'
    )->withPivot('status');
}

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tecnologias' => 'array',
        'is_admin' => 'boolean',
        'approval_requested_at' => 'datetime',
        'approval_reviewed_at' => 'datetime'
    ];

    public function atividades()
    {
        return $this->hasMany(Atividade::class);
    }

    public function isVerifiedProfessor(): bool
    {
        return $this->tipo === 'professor' && $this->approval_status === 'approved';
    }
}
