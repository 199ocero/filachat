<?php

namespace JaOcero\FilaChat\Traits;

trait CanValidateDocument
{
    protected $validDocumentExtensions = [
        'pdf',    // For application/pdf
        'doc',    // For application/msword
        'docx',   // For application/vnd.openxmlformats-officedocument.wordprocessingml.document
        'csv',    // For text/csv
        'txt',    // For text/plain
        'xls',    // For application/vnd.ms-excel
        'xlsx',   // For application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        'ppt',    // For application/vnd.ms-powerpoint
        'pptx',   // For application/vnd.openxmlformats-officedocument.presentationml.presentation
    ];

    public function validateDocument(string $documentPath): bool
    {
        $extension = strtolower(pathinfo($documentPath, PATHINFO_EXTENSION));

        if (in_array($extension, $this->validDocumentExtensions)) {
            return true;
        }

        return false;
    }
}
