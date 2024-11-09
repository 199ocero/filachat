<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FilaChatGroup extends Model
{
    use SoftDeletes;

    protected $table = 'filachat_groups';

    protected $fillable = [
        'name',
        'created_by',
        'owner_id',
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
