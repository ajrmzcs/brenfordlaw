<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BenfordLawCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::SetUp();
        $this->data = json_decode(file_get_contents(base_path('tests/Stubs/data.json')), true);
    }

    /** @test */
    public function it_runs_successfully_with_provided_dataset(): void
    {
        $dataset = implode(' ', $this->data['good']);

        $this->artisan('data-science:benford-law-checker ' . $dataset)
            ->expectsQuestion('Please enter a variance number', 2)
            ->assertExitCode(0);
    }

    /** @test */
    public function it_asks_for_options_when_dataset_is_not_provided(): void
    {
        $this->artisan('data-science:benford-law-checker')
            ->expectsChoice(
                "Since you didn't provide a dataset, which data source would you like to use?",
                '1000 Random integers (In most cases does not comply)',
                [
                    'US. 2019 census population per county',
                    '1000 Random integers (In most cases does not comply)',
                ]
            )
            ->expectsQuestion('Please enter a variance number', 2)
            ->assertExitCode(0);
    }

    /** @test */
    public function it_validates_dataset(): void
    {
        $dataset = implode(' ', ['a', 'b', 'c', 'd']);
        $this->artisan('data-science:benford-law-checker ' . $dataset)
            ->expectsQuestion('Please enter a variance number', 2)
            ->expectsOutput('The dataset.0 field must be an integer.')
            ->expectsOutput('The dataset.1 field must be an integer.')
            ->expectsOutput('The dataset.2 field must be an integer.')
            ->expectsOutput('The dataset.3 field must be an integer.')
            ->expectsOutput('Process aborted.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_validates_variance(): void
    {
        $dataset = implode(' ', $this->data['good']);
        $this->artisan('data-science:benford-law-checker ' . $dataset)
            ->expectsQuestion('Please enter a variance number', 'a')
            ->expectsOutput('The variance field must be a number.')
            ->expectsOutput('Process aborted.')
            ->assertExitCode(0);
    }
}
