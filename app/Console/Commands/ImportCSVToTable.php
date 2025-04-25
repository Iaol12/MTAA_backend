<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportCSVToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importStations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = 'stations';
        $filePath = storage_path("app/{$table}.csv");

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($headers, $row);
            DB::table($table)->insert($record);  // you can also use upsert if needed
        }

        DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}) + 1, 1), false)");

        fclose($handle);

        $this->info("Data imported into table: {$table}");
    }
}
