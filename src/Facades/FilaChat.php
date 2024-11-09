<?php

namespace JaOcero\FilaChat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JaOcero\FilaChat\FilaChat
 */
class FilaChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \JaOcero\FilaChat\FilaChat::class;
    }
}
