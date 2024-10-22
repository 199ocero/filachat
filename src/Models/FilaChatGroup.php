<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FilaChatGroup extends Model
{
    protected $table = 'filachat_groups';

    protected $fillable = [
        'name',
        'created_by',
    ];


    public function isAgent(): bool
    {
        return false;
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(FilaChatConversation::class, 'receiverable_id', 'id')
            ->where('receiverable_type', FilaChatGroup::class);
    }


    public function members(): HasMany
    {
        return $this->hasMany(FilaChatGroupMember::class, 'group_id');
    }

}
