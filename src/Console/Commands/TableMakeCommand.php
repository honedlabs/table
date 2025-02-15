<?php

namespace Honed\Table\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:table')]
class TableMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    protected $name = 'make:table';

    protected $description = 'Create a new table class';

    protected $type = 'Table';

    protected function getStub(): string
    {
        $stub = '/stubs/table.php.stub';

        return $this->resolveStubPath($stub);
    }

    /**
     * @param  string  $stub
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../../..'.$stub;
    }

    /**
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Tables';
    }

    /**
     * @param  string  $name
     */
    protected function buildClass($name): string
    {
        $tableNamespace = $this->getNamespace($name);

        $replace = [];

        $replace["use {$tableNamespace};\n"] = '';

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    /**
     * @param  string  $table
     */
    protected function parseModel($table): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $table)) {
            throw new InvalidArgumentException('Table name contains invalid characters.');
        }

        return $this->qualifyModel($table);
    }

    /**
     * @return array<int,array<int,mixed>>
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the table already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Create a new model for the table'],
        ];
    }
}
