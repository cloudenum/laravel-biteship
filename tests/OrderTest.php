<?php

namespace Cloudenum\Biteship\Tests;

use Cloudenum\Biteship\Exceptions\RequestException;
use Cloudenum\Biteship\Order;

class OrderTest extends TestCase
{
    public function testCreateOrder()
    {
        $data = [
            'shipper_contact_name' => 'Biteship Indonesia',
            'shipper_contact_email' => 'Biteship@gmail.com',
            'shipper_contact_phone' => '08170078120',
            'shipper_organization' => 'Biteship',
            'origin_address' => 'Plaza Senayan, Jalan Asia Afrika, RT.1/RW.3',
            'origin_contact_name' => 'Akbar',
            'origin_contact_phone' => '08170078120',
            'origin_coordinate' => [
                'latitude' => -6.2253114,
                'longitude' => 106.7993735,
            ],
            'origin_note' => 'Deket pintu masuk STC',
            'origin_postal_code' => 12440,
            'destination_address' => 'Lebak Bulus MRT, Jalan R.A.Kartini',
            'destination_contact_name' => 'Bambang',
            'destination_contact_phone' => '08170032123',
            'destination_contact_email' => 'mirsa@biteship.com',
            'destination_coordinate' => [
                'latitude' => -6.28927,
                'longitude' => 106.77492000000007,
            ],
            'destination_note' => 'Di deket pintu MRT',
            'destination_postal_code' => 12950,
            'courier_company' => 'anteraja',
            'courier_type' => 'reg',
            'courier_insurance' => 500000,
            'delivery_type' => 'later',
            'order_note' => 'Please be careful',
            'items' => [
                [
                    'name' => 'Black L',
                    'description' => 'Feast/Bangkok\'19 Invasion',
                    'value' => 165000,
                    'quantity' => 1,
                    'length' => 10,
                    'width' => 10,
                    'height' => 10,
                    'weight' => 200,
                ],
            ],
        ];

        $this->mockApiResponse(<<<'JSON'
        {
            "success": true,
            "message": "Order successfully created",
            "object": "order",
            "id": "5dd599ebdefcd4158eb8470b",
            "shipper": {
                "name": "Biteship Indonesia",
                "email": "Biteship@gmail.com",
                "phone": "08170078120",
                "organization": "Biteship"
            },
            "origin": {
                "contact_name": "Akbar",
                "contact_phone": "08170078120",
                "coordinate": {
                    "latitude": -6.2253114,
                    "longitude": 106.7993735
                },
                "address": "Plaza Senayan, Jalan Asia Afrika, RT.1/RW.3",
                "note": "Deket pintu masuk STC",
                "postal_code": 12440
            },
            "destination": {
                "contact_name": "Bambang",
                "contact_phone": "08170032123",
                "contact_email": "mirsa@biteship.com",
                "address": "Lebak Bulus MRT, Jalan R.A.Kartini",
                "note": "Di deket pintu MRT",
                "proof_of_delivery": {
                    "use": false,
                    "fee": 0,
                    "note": null,
                    "link": null
                },
                "cash_on_delivery": {
                    "id": "77bb0f60b029822ecb1411da",
                    "amount": 500000,
                    "fee": 20000,
                    "note": null,
                    "type": "7_days"
                },
                "coordinate": {
                    "latitude": -6.28927,
                    "longitude": 106.77492000000007
                },
                "postal_code": 12950
            },
            "courier": {
                "tracking_id": "6de509ebdefgh4158ij3451c",
                "waybill_id": null,
                "company": "anteraja",
                "name": null,
                "phone": null,
                "driver_name": null,
                "driver_phone": null,
                "driver_photo_url": null,
                "driver_plate_number": null,
                "type": "reg",
                "link": null,
                "insurance": {
                    "amount": 500000,
                    "fee": 2500,
                    "note": ""
                },
                "routing_code": null
            },
            "delivery": {
                "datetime": "2029-09-24T12:00+07:00",
                "note": null,
                "type": "later",
                "distance": 9.8,
                "distance_unit": "kilometer"
            },
            "reference_id": null,
            "items": [
                {
                    "name": "Black L",
                    "description": "Feast/Bangkok'19 Invasion",
                    "sku": null,
                    "value": 165000,
                    "quantity": 1,
                    "length": 10,
                    "width": 10,
                    "height": 10,
                    "weight": 200
                }
            ],
            "extra": [],
            "price": 48000,
            "metadata": {},
            "note": "Please be careful",
            "status": "confirmed"
        }
        JSON);
        $order = Order::create($data);

        $this->assertEquals('5dd599ebdefcd4158eb8470b', $order->id);
        $this->assertEquals('Biteship Indonesia', $order->shipper['name']);
        $this->assertCount(1, $order->items);
    }

    public function testCreateOrderWithInvalidData()
    {
        $data = [

        ];

        $this->expectException(RequestException::class);
        $this->expectExceptionCode(400);

        Order::create($data);
    }

    public function testFindNonExistingOrder()
    {
        $orderId = 'non-existing-order-id';

        $this->expectException(RequestException::class);

        Order::find($orderId);
    }

    public function testCancelNonExistingOrder()
    {
        $orderId = 'non-existing-order-id';
        $reason = 'Out of stock';

        $this->expectException(RequestException::class);

        Order::find($orderId)->cancel($reason);
    }
}
