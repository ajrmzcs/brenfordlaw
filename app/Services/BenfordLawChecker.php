<?php

namespace App\Services;

use App\Exceptions\InvalidValueException;

class BenfordLawChecker
{
    /**
     * @throws InvalidValueException
     */
    public function __construct(
        private array $values,
    ) {
        $this->values = $this->integerValidator($this->values);
    }

    public function analyze(): object
    {
        return $this->checkCompliance($this->countNumbersByStartingDigit());
    }

    /**
     * Validates and converts all values into integers
     * @param array $values
     * @return array
     * @throws InvalidValueException
     */
    private function integerValidator(array $values): array
    {
        return array_map(function ($value): int {
            $integer = filter_var($value, FILTER_VALIDATE_INT);
            if (!$integer) {
                throw new InvalidValueException(
                    "All provided values must be positives integers: $value is invalid"
                );
            }
            return $integer;
        }, $values);
    }

    private function countNumbersByStartingDigit(): array
    {
        $result = array_fill(1, 9, 0);
        foreach ($this->values as $value) {
            $firstDigit = $this->getFirstDigit($value);
            $result[$firstDigit]++;
        }
    }

    private function getFirstDigit(int $value): int
    {
        while ($value >= 10) {
            $value /= 10;
        }
        return (int) $value;
    }

    private function checkCompliance(array $valueDistribution): object
    {

        $result = [
            'distribution' => [
                1 => [
                    'total' => $valueDistribution[1],
                    'percentage' => 30,
                    'compliance' => true,
                ],
            ],
            'final_compliance' => true,
        ];

        return (object) $result;
    }



}
