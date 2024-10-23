<?php

namespace JaOcero\FilaChat\Commands;

use Illuminate\Console\Command;
use JaOcero\FilaChat\Models\FilaChatAgent;

use function Laravel\Prompts\text;

class FilaChatCreateAgentCommand extends Command
{
    public $signature = 'filachat:agent-create';

    public $description = 'Create a new agent';

    public function handle(): int
    {
        $this->info('Creating new agent...');
        $this->createAgent();

        return self::SUCCESS;
    }

    protected function createAgent()
    {
        // Prompt for agentable ID
        $agentableId = text(
            required: true,
            label: 'Enter the agent ID',
            placeholder: 'E.g. 24',
            hint: 'This will be used to identify the agent belongs to ' . config('filachat.agent_model') . ' model.',
            validate: function ($value) {
                if (! is_numeric($value)) {
                    return 'The ID must be a number';
                }

                if ($value <= 0) {
                    return 'The ID must be greater than 0';
                }
            }
        );

        // Check if the agentable ID exists in the database
        $isExistingRecord = config('filachat.agent_model')::find($agentableId);

        if (! $isExistingRecord) {
            $this->error('Agent using this ID with model ' . config('filachat.agent_model') . ' does not exist.');

            return;
        }

        // Check if the combination already exists
        $existingAgent = FilaChatAgent::where('agentable_id', $agentableId)
            ->where('agentable_type', config('filachat.agent_model'))
            ->first();

        if ($existingAgent) {
            $this->error('Agent using this ID with model ' . config('filachat.agent_model') . ' already exists.');

            return;
        }

        // Create the new agent
        FilaChatAgent::create([
            'agentable_id' => $agentableId,
            'agentable_type' => config('filachat.agent_model'),
        ]);

        $this->info('Agent created successfully.');
    }
}
