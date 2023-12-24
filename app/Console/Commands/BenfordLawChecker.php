<?php

namespace App\Console\Commands;

use App\Exceptions\InvalidConfigException;
use App\Exceptions\InvalidValueException;
use App\Services\BenfordLawService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BenfordLawChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "data-science:benford-law-checker {dataset?* : Space-separated integer. Don't worry if you don't have one. We can provide sample data :)}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Validates if is the provided numbers complies with the Benford's Law";

    /**
     * Execute the console command.
     * @throws InvalidConfigException|ValidationException
     */
    public function handle(BenfordLawService $benfordLawService)
    {
        $this->info("Welcome to Benford's Law Checker");
        $this->newLine(2);

        $dataset = $this->argument('dataset');

        if (!$dataset) {
            $this->line('Please select an option:');
            $option = $this->choice(
                "Since you didn't provide a dataset, which data source would you like to use?",
                [
                    'US. 2019 census population per county',
                    '1000 Random integers (In most cases does not comply)',
                ],
                0,
                4
            );

            $dataset = match ($option) {
                'US. 2019 census population per county' => $this->getCensusData(),
                '1000 Random integers (In most cases does not comply)' => $this->generateRandomDataset(),
            };
        }

        $variance = $this->ask('Please enter a variance number', 2);

        $data = $this->validateRequiredData($dataset, $variance);
        if (!$data) {
            $this->newLine(2);
            $this->error('Process aborted.');
            return;
        }

        $result = $benfordLawService->generateResults($data['dataset'], $data['variance']);

        $this->table(
            ['Starting Digit', 'Occurrences', 'Occurrence %', 'Benford %', 'Complies'],
            $result
        );

        $this->newLine(2);
        $this->info("Benford's Law Checker: Finished");
    }

    /**
     * Validates provided and config data
     * @param array $dataset
     * @param string $variance
     * @return array
     * @throws ValidationException
     */
    private function validateRequiredData(array $dataset, string $variance): array
    {
        $validator = Validator::make(
            [
                'dataset' => $dataset,
                'variance' => $variance,
            ],
            [
                'dataset' => 'required|array',
                'dataset.*' => 'required|integer',
                'variance' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            $this->info('Data/Config Validation errors:');
            $this->newLine();
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return [];
        }

        return $validator->validated();
    }

    /**
     * Generates a 1000 random integer array
     * @return array
     * @throws Exception
     */
    private function generateRandomDataset(): array
    {
        $random = [];
        for ($i = 0; $i < 1000; $i++) {
            $random[] = random_int(1, 10000);
        }

        return $random;
    }

    /**
     * Fetch 2019's US population per county
     * @return array
     */
    private function getCensusData(): array
    {
        $response = Http::get('https://api.census.gov/data/2019/pep/charagegroups?get=POP&for=county');
        $rawResponse = json_decode($response->body());
        // Remove first row that contains fields description
        unset($rawResponse[0]);

        return array_map(fn ($row) => filter_var($row[0], FILTER_VALIDATE_INT), $rawResponse);
    }
}
