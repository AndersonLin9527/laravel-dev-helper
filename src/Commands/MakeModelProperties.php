<?php

namespace Anderson9527\LaravelDevHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

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
    protected $description = 'Make Model @properties for PHPDOC';

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
     * @return int
     * php artisan x-anderson9527:make-model-properties
     * php artisan x-anderson9527:make-model-properties members
     */
    public function handle(): int
    {
        $schema = Schema::connection(config('database.default', 'mysql'));

        // 詢問 Database Table
        $inputDatabaseTable = $this->argument('inputDatabaseTable');
        do {
            if (is_null($inputDatabaseTable)) {
                $inputDatabaseTable = $this->ask('Table name ?');
            }
            if (!$schema->hasTable($inputDatabaseTable)) {
                $this->error('Table name : ' . $inputDatabaseTable . ' does not exist !');
                $inputDatabaseTable = null;
            }
        } while ($inputDatabaseTable == null);
        $this->info('$inputDatabaseTable: ' . $inputDatabaseTable . PHP_EOL);

        $tableColumns = $schema->getColumnListing($inputDatabaseTable);
        foreach ($tableColumns as $tableColumn) {
            $this->line('@property mixed ' . $tableColumn);
        }
        $this->line('');
        foreach ($tableColumns as $tableColumn) {
            $this->line("'" . $tableColumn . "',");
        }
        return 1;
    }
}
