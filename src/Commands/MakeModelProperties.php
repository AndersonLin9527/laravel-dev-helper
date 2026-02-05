<?php

namespace Anderson9527\LaravelDevHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Class MakeModelProperties
 */
class MakeModelProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'x-anderson9527:make-model-properties {inputDatabaseTable?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Model @properties for PHPDoc';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @example php artisan x-anderson9527:make-model-properties
     * @example php artisan x-anderson9527:make-model-properties members
     */
    public function handle(): int
    {
        $defaultConnection = config('database.default', 'mysql');
        $defaultDatabase = config('database.connections.' . $defaultConnection . '.database');
        $schema = Schema::connection($defaultConnection);
        // 詢問 Database Table
        $inputDatabaseTable = $this->argument('inputDatabaseTable');
        do {
            if (is_null($inputDatabaseTable)) {
                $allTableInfos = $schema->getTables($defaultDatabase);
                $allTableNames = collect($allTableInfos)->pluck('name')->all();

                $inputDatabaseTable = $this->choice(
                    'Please select a table',
                    $allTableNames
                );
            }
            if (!$schema->hasTable($inputDatabaseTable)) {
                $this->error('Table name : ' . $inputDatabaseTable . ' does not exist !');
                $inputDatabaseTable = null;
            }
        } while ($inputDatabaseTable == null);
        $this->info('$inputDatabaseTable: ' . $inputDatabaseTable . PHP_EOL);

        $this->info('PHPDoc @property:' . PHP_EOL);
        $columns = $schema->getColumns($inputDatabaseTable);
        foreach ($columns as $column) {
            $name = $column['name'];
            $type = $this->mapTypeName($column['type_name']);
            $nullable = $column['nullable'] ?? false;
            $phpDocType = $nullable && $type !== 'mixed' ? $type . '|null' : $type;
            $this->line("@property $phpDocType \$$name");
        }

        $this->info(PHP_EOL . '$fillable:' . PHP_EOL);
        foreach ($columns as $column) {
            $name = $column['name'];
            $this->line("'" . $name . "',");
        }

        return SymfonyCommand::SUCCESS;
    }

    protected function mapTypeName(string $typeName): string
    {
        return match (strtolower($typeName)) {
            'bigint', 'int', 'mediumint', 'smallint', 'tinyint' => 'int',
            'boolean' => 'bool',
            'char', 'longtext', 'mediumtext', 'text', 'varchar' => 'string',
            'date', 'datetime', 'time', 'timestamp' => 'string', // 用 string 表示時間
            'decimal', 'double', 'float' => 'float',
            'json' => 'array',
            default => 'mixed',
        };
    }

}
