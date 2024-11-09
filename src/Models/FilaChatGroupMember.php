<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FilaChatGroupMember extends Model
{
    use SoftDeletes;

    protected $table = 'filachat_group_members';

    protected $fillable = [
        'group_id',
        'member_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FilaChatGroup::class, 'group_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(config('filachat.member_model'), 'member_id');
    }
}
