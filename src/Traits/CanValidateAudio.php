<?php

namespace JaOcero\FilaChat\Traits;

trait CanValidateAudio
{
    protected $validAudioExtensions = [
        'm4a',  // For audio/m4a
        'wav',  // For audio/wav
        'mp3',  // For audio/mpeg (commonly associated with MP3 files)
        'ogg',  // For audio/ogg
        'aac',  // For audio/aac
        'flac', // For audio/flac
        'midi', // For audio/midi (alternative extension: .mid)
    ];

    public function validateAudio(string $imagePath): bool
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if (in_array($extension, $this->validAudioExtensions)) {
            return true;
        }

        return false;
    }
}
