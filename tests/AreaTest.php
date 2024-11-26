<?php

namespace Cloudenum\Biteship\Tests;

use Cloudenum\Biteship\Area;
use Illuminate\Support\Collection;

class AreaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'biteship' => ['api_key' => 'test'],
        ]);
    }

    public function test_search_returns_collection()
    {
        $this->mockApiResponse(['success' => true, 'areas' => []]);

        $result = Area::search('Jakarta');
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_search_with_empty_result()
    {
        $this->mockApiResponse(['success' => true, 'areas' => []]);

        $result = Area::search('Jakarta');
        $this->assertTrue($result->isEmpty());
    }

    public function test_search_with_valid_data()
    {
        $mockedAreas = [
            [
                'id' => '1',
                'name' => 'Central Jakarta',
                'country_name' => 'Indonesia',
                'country_code' => 'ID',
                'administrative_division_level_1_name' => 'Jakarta',
                'administrative_division_level_1_type' => 'Province',
                // Other fields omitted for brevity
            ],
        ];
        $this->mockApiResponse(['success' => true, 'areas' => $mockedAreas]);

        $result = Area::search('Jakarta');
        $this->assertFalse($result->isEmpty());
        $this->assertInstanceOf(Area::class, $result->first());
        $this->assertEquals('Central Jakarta', $result->first()->name);
    }

    public function test_search_expect_exception()
    {
        $this->mockApiResponse(['success' => false], 500);

        $this->expectException(\Illuminate\Http\Client\HttpClientException::class);
        $this->expectExceptionCode(500);
        Area::search('ErrorCase');
    }
}
