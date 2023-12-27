<?php

namespace Tests\Feature;

use App\Services\BenfordLawService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BenfordLawServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::SetUp();
        $this->data = json_decode(file_get_contents(base_path('tests/Stubs/data.json')), true);
    }

    /** @test */
    public function it_generates_result_table_with_compliance_with_a_good_dataset(): void
    {
        $results = (new BenfordLawService(config('benford.distribution')))->generateResults($this->data['good'], 2);
        foreach ($results as $index => $result) {
            $this->assertEquals($index, $result['index']);
            $this->assertEquals('Yes', $result['complies']);
        }
    }

    /** @test */
    public function it_generates_result_table_with_no_compliance_with_a_not_good_dataset(): void
    {
        $results = (new BenfordLawService(config('benford.distribution')))->generateResults($this->data['not-good'], 2);
        foreach ($results as $index => $result) {
            $this->assertEquals($index, $result['index']);
            $this->assertEquals('No', $result['complies']);
        }
    }

    /** @test */
    public function it_generates_result_table_with_a_dataset_that_doesnt_have_all_digits(): void
    {
        // The incomplete dataset doesn't have numbers starting in 8 or 9
        $results = (new BenfordLawService(config('benford.distribution')))->generateResults($this->data['incomplete'], 2);

        $this->assertEquals(8, $results[8]['index']);
        $this->assertEquals(0, $results[8]['occurrences']);
        $this->assertEquals(0, $results[8]['percentage']);
        $this->assertEquals('No', $results[8]['complies']);

        $this->assertEquals(9, $results[9]['index']);
        $this->assertEquals(0, $results[9]['occurrences']);
        $this->assertEquals(0, $results[9]['percentage']);
        $this->assertEquals('No', $results[9]['complies']);
    }
}
