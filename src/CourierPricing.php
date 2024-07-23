<?php

namespace Cloudenum\Biteship;

/**
 * @property array|null $origin
 * @property array|null $destination
 * @property array|null $pricing
 */
class CourierPricing extends BiteshipObject
{
    protected static string $apiUri = '/v1/rates/couriers';

    protected array $dynamicProperties = [
        'origin',
        'destination',
        'pricing',
    ];

    /**
     * Get the delivery prices for couriers
     *
     * @return \Illuminate\Support\Collection
     *
     * @see https://biteship.com/id/docs/api/rates/retrieve
     */
    public static function Rates(array $data)
    {
        $data = \Illuminate\Support\Arr::whereNotNull($data);

        $response     = Biteship::api()->post(self::$apiUri, $data);
        $responseJson = $response->json();

        return collect($responseJson)->map(function (array $attributes) {
            return new static($attributes);
        });
    }
}
