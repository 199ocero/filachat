<?php

namespace JaOcero\FilaChat\Traits;

trait CanValidateVideo
{
    protected $validVideoExtensions = [
        'mp4',      // For video/mp4
        'avi',      // For video/avi
        'mov',      // For video/quicktime
        'webm',     // For video/webm
        'mkv',      // For video/x-matroska
        'flv',      // For video/x-flv
        'mpeg',     // For video/mpeg
        'mpg',      // For video/mpeg (alternative extension)
    ];

    public function validateVideo(string $imagePath): bool
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if (in_array($extension, $this->validVideoExtensions)) {
            return true;
        }

        return false;
    }
}
