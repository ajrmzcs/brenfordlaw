<?php

namespace App\Services;

class BenfordLawService
{
    public function __construct(
        private readonly array $benfordDistribution,
    ) {}

    /**
     * Generate the Benford Law Checker table results
     * @param array $values
     * @param int $variance
     * @return array
     */
    public function generateResults(array $values, int $variance): array
    {
        // Dataset elements total
        $datasetCount = count($values);

        // Total of elements by starting digit
        $datasetFirstDigits = array_map(fn ($value) => $this->getFirstDigit($value), $values);

        // Total occurrences count by starting digit
        $occurrenceCount = array_count_values($datasetFirstDigits);

        // Total occurrences percentage by starting digit array
        $occurrencePercentage = array_map(
            fn ($occurrenceByIndex): float => round(($occurrenceByIndex / $datasetCount) * 100, 3),
            $occurrenceCount
        );

        // Initialize a sorted by index array
        $data = array_fill(1, 9, []);

        foreach($data as $key => $value) {
            $data[$key] = [
                'index' => $key,
                'occurrences' => $occurrenceCount[$key],
                'percentage' => $occurrencePercentage[$key],
                'reference' => $this->benfordDistribution[$key],
                'complies' => $this->checkBenfordLawCompliance($key, $occurrencePercentage[$key], $variance) ? 'Yes' : 'No',
            ];
        }

        return $data;
    }

    /**
     * Returns starting digit of a given integer
     * @param int $value
     * @return int
     */
    private function getFirstDigit(int $value): int
    {
        while ($value >= 10) {
            $value /= 10;
        }
        return $value;
    }

    /**
     * Checks Benford's Law compliance
     * @param int $key
     * @param float $occurrencePercentage
     * @param float $variance
     * @return bool
     */
    private function checkBenfordLawCompliance(int $key, float $occurrencePercentage, float $variance): bool
    {
        return pow($occurrencePercentage - $this->benfordDistribution[$key], 2) < $variance;
    }
}
