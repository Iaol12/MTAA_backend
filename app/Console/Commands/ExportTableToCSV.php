<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExportTableToCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exportStations';

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
    $fileName = storage_path("app/{$table}.csv");

    $data = DB::table($table)->get();

    if ($data->isEmpty()) {
        $this->warn("No data found in table {$table}");
        return;
    }

    $file = fopen($fileName, 'w');

    // Headers
    fputcsv($file, array_keys((array) $data[0]));

    // Rows
    foreach ($data as $row) {
        fputcsv($file, (array) $row);
    }

    fclose($file);

    $this->info("Exported to: {$fileName}");
}
}
