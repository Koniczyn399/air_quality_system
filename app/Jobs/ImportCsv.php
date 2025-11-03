<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportCsv implements ShouldQueue
{
 use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    /**
     * Create a new job instance.
     */

   
    public function __construct()
    {
        //
    }
 
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //storage_path('app/public/products.csv')
        SimpleExcelReader::create(storage_path('app/public/products.csv'))
            ->useDelimiter(';')
            ->useHeaders(['ID', 'title', 'description'])
            ->getRows()
            ->chunk(5000)
            ->each(
               fn ($chunk) => ImportProductChunk::dispatch($chunk)
            );
    }
}