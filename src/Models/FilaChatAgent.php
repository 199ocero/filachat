<?php

namespace JaOcero\FilaChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FilaChatAgent extends Model
{
    protected $table = 'filachat_agents';

    protected $fillable = [
        'agentable_id',
        'agentable_type',
    ];

    public function agentable(): MorphTo
    {
        return $this->morphTo();
    }
}
