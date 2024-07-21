<?php

namespace JaOcero\FilaChat\Traits;

trait CanGetOriginalFileName
{
    public function getOriginalFileName(string $path, array $names): string
    {
        return $names[$path] ?? 'File name not found';
    }
}
