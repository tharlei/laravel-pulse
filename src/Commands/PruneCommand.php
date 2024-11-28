<?php

namespace Laravel\Pulse\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Laravel\Pulse\Pulse;

/**
 * @internal
 */
class PruneCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The command's signature.
     *
     * @var string
     */
    protected $signature = 'pulse:prune {--hours=24 : The number of hours to retain Pulse data}';

    /**
     * The command's description.
     *
     * @var string
     */
    protected $description = 'Prune stale entries from the Prune database';

    /**
     * Handle the command.
     */
    public function handle(Pulse $pulse): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $pulse->prune(
            now()->subHours(
                (int) $this->option('hours')
            )
        );

        return Command::SUCCESS;
    }
}
