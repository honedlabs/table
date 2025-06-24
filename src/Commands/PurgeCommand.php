<?php

declare(strict_types=1);

namespace Honed\Table\Commands;

use Honed\Table\Facades\Views;
use Honed\Table\ViewManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'views:purge', aliases: ['views:clear'])]
class PurgeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'views:purge
        {tables?* : The tables to purge}
        {--store= : The store to purge the tables from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete views from storage';

    /**
     * The console command name aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = ['views:clear'];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ViewManager $manager)
    {
        /** @var string|null */
        $store = $this->option('store');

        $store = $manager->store($store);

        /** @var array<int, class-string<\Honed\Table\Table>>|null */
        $tables = $this->argument('tables') ?: null; // @phpstan-ignore varTag.nativeType

        if ($tables) {
            $tables = array_map(
                static fn ($table) => Views::serializeTable($table),
                (array) $tables
            );
        }

        $store->purge($tables);

        if ($tables) {
            $this->components->info(implode(', ', $tables).' successfully purged from storage.');
        } else {
            $this->components->info('All views successfully purged from storage.');
        }

        return self::SUCCESS;
    }
}
