<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;

class FilaChatRole extends Model
{
    protected $table = 'fila_chat_roles';

    protected $fillable = [
        'name',
    ];
}
