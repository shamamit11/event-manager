<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CustomMakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-migration {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file in a custom directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = '/app/Infrastructure/Persistence/Migrations';

        $this->call('make:migration', [
            'name' => $name,
            '--path' => $path,
        ]);

        $this->info('Migration created successfully in ' . $path);
    }
}
