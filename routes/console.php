<?php

use App\Support\Import\WordpressCustomerImporter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('import:wordpress-customers {file=storage/app/imports/customers.csv} {--apply : Persist changes (default is dry-run)}', function () {
    $fileArgument = (string) $this->argument('file');
    $csvPath = str_starts_with($fileArgument, DIRECTORY_SEPARATOR)
        || preg_match('/^[A-Za-z]:\\\\/', $fileArgument)
        ? $fileArgument
        : base_path($fileArgument);

    $dryRun = ! $this->option('apply');

    $this->info($dryRun
        ? 'Running customer import in DRY-RUN mode...'
        : 'Running customer import in APPLY mode...');
    $this->line("Source file: {$csvPath}");

    $result = app(WordpressCustomerImporter::class)->importCsv($csvPath, $dryRun);

    $this->newLine();
    $this->info('Import summary');
    $this->table(
        ['Metric', 'Count'],
        [
            ['Rows processed', $result['rows_total']],
            ['Users created', $result['users_created']],
            ['Users updated', $result['users_updated']],
            ['Customers created', $result['customers_created']],
            ['Customers updated', $result['customers_updated']],
            ['Addresses created', $result['addresses_created']],
            ['Addresses updated', $result['addresses_updated']],
            ['Failed rows', $result['failed']],
        ]
    );

    if (! empty($result['errors'])) {
        $this->newLine();
        $this->warn('Row errors');
        foreach (array_slice($result['errors'], 0, 25) as $error) {
            $this->line("- {$error}");
        }
        if (count($result['errors']) > 25) {
            $this->line('...additional errors omitted');
        }
    }
})->purpose('Import WordPress customers into Laravel + Lunar');
