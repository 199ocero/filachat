<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;

class FilaChatRole extends Model
{
    protected $table = 'filachat_roles';

    protected $fillable = [
        'name',
    ];
}
