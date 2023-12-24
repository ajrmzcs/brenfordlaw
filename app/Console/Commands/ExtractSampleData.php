<?php

namespace App\Console\Commands;

use App\Exceptions\InvalidConfigException;
use App\Exceptions\InvalidValueException;
use App\Services\BenfordLawService;
use Illuminate\Console\Command;

class ExtractSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-science:extract-sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Extract Sample data to test Benford's Law";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $random = [];
        for ($i = 0; $i < 100; $i++) {
            $random[] = random_int(1, 1000);
        }
        dd(json_encode($random));
    }
}
