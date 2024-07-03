<?php

namespace JaOcero\FilaChat\Commands;

use Illuminate\Console\Command;

class FilaChatCommand extends Command
{
    public $signature = 'filachat';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
