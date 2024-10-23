<?php

namespace JaOcero\FilaChat\Traits;

trait CanValidateImage
{
    protected $validImageExtensions = [
        'png',  // For image/png
        'jpeg', // For image/jpeg
        'jpg',  // For image/jpg
        'gif',  // For image/gif
    ];

    public function validateImage(string $imagePath): bool
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if (in_array($extension, $this->validImageExtensions)) {
            return true;
        }

        return false;
    }
}
