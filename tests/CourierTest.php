<?php
namespace Cloudenum\Biteship\Tests;

use Cloudenum\Biteship\Courier;

class CourierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'biteship' => [
                'api_key' => 'test',
            ],
        ]);
    }

    public function testAllMethodReturnsCollectionWithCorrectData()
    {
        $this->mockApiResponse(<<<JSON
            {
                "success": true,
                "object": "courier",
                "couriers": [
                    {
                        "available_for_cash_on_delivery": false,
                        "available_for_proof_of_delivery": false,
                        "available_for_instant_waybill_id": true,
                        "courier_name": "Grab",
                        "courier_code": "grab",
                        "courier_service_name": "Instant",
                        "courier_service_code": "instant",
                        "tier": "premium",
                        "description": "On Demand Instant (bike)",
                        "service_type": "same_day",
                        "shipping_type": "parcel",
                        "shipment_duration_range": "1 - 3",
                        "shipment_duration_unit": "hours"
                    },
                    {
                        "available_for_cash_on_delivery": false,
                        "available_for_proof_of_delivery": false,
                        "available_for_instant_waybill_id": true,
                        "courier_name": "Grab",
                        "courier_code": "grab",
                        "courier_service_name": "Same Day",
                        "courier_service_code": "same_day",
                        "tier": "premium",
                        "description": "On Demand within 8 hours (bike)",
                        "service_type": "same_day",
                        "shipping_type": "parcel",
                        "shipment_duration_range": "6 - 8",
                        "shipment_duration_unit": "hours"
                    }
                ]
            }
        JSON);

        $couriers = Courier::all();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $couriers);
        $this->assertCount(2, $couriers);

        $courier = $couriers->first();
        $this->assertInstanceOf(Courier::class, $courier);
        $this->assertEquals('grab', $courier->courier_code);
    }
}

