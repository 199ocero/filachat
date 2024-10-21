<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilaChatGroup extends Model
{
    protected $table = 'filachat_groups';

    protected $fillable = [
        'name',
    ];


    public function isAgent(): bool
    {
        return false;
    }


    public function members(): HasMany
    {
        return $this->hasMany(FilaChatGroupMember::class, 'group_id');
    }

}
