<?php
 
namespace App\Jobs;
 
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
 
class ImportProductChunk implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    public $uniqueFor = 2137;
 
    /**
     * Create a new job instance.
     */
    public function __construct(
        public $chunk
    ) {
        //
    }
 
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->chunk->each(function (array $row) {



            Model::withoutTimestamps(fn () => Product::updateOrCreate([
                'product_id' => $row['ID'],
                'title' => $row['title'],
                'description' => $row['description'],
           ]));




           
        });
    }
 
    public function uniqueId(): string
    {
        return Str::uuid()->toString();
    }
}