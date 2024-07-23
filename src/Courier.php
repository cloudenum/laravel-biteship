<?php

namespace Cloudenum\Biteship;

/**
 * @property array|null $available_collection_method
 * @property bool $available_for_cash_on_delivery
 * @property bool $available_for_proof_of_delivery
 * @property bool $available_for_instant_waybill_id
 * @property string|null $courier_name
 * @property string|null $courier_code
 * @property string|null $courier_service_name
 * @property string|null $courier_service_code
 * @property string|null $tier
 * @property string|null $description
 * @property string|null $service_type
 * @property string|null $shipping_type
 * @property string|null $shipment_duration_range
 * @property string|null $shipment_duration_unit
 */
class Courier extends BiteshipObject
{
    protected static string $apiUri = '/v1/couriers';

    protected array $dynamicProperties = [
        'available_collection_method',
        'available_for_cash_on_delivery',
        'available_for_proof_of_delivery',
        'available_for_instant_waybill_id',
        'courier_name',
        'courier_code',
        'courier_service_name',
        'courier_service_code',
        'tier',
        'description',
        'service_type',
        'shipping_type',
        'shipment_duration_range',
        'shipment_duration_unit',
    ];

    /**
     * Get the list of avalaible couriers
     *
     * @return \Illuminate\Support\Collection<Courier>
     *
     * @see https://biteship.com/id/docs/api/couriers/retrieve
     */
    public static function all(): \Illuminate\Support\Collection
    {
        $response = Biteship::api()->get(self::$apiUri);
        $responseJson = $response->json();

        return collect($responseJson['couriers'])->map(function (array $attributes) {
            return new static($attributes);
        });
    }
}
