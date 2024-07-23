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
        "origin",
        "destination",
        "pricing",
    ];

    /**
     * Get the delivery prices for couriers
     *
     * @param array $data
     * @return \Illuminate\Support\Collection
     *
     * @see https://biteship.com/id/docs/api/rates/retrieve
     */
    public static function Rates(array $data)
    {
        $validator = validator($data, [
            'origin_area_id ' => ['required_without:origin_latitude,origin_longitude,origin_postal_code', 'string'],
            'origin_latitude' => ['required_without:origin_area_id,origin_postal_code', 'string'],
            'origin_longitude' => ['required_without:origin_area_id,origin_postal_code', 'string'],
            'origin_postal_code' => ['required_without:origin_area_id,origin_latitude,origin_longitude', 'string'],
            'type' => ['in:origin_suggestion_to_closest_destination'],
            'couriers' => ['required', 'string'],
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.sku' => ['nullable', 'string'],
            'items.*.value' => ['required', 'numeric'],
            'items.*.quantity' => ['required', 'integer'],
            'items.*.weight' => ['required', 'numeric'],
            'items.*.height' => ['nullable', 'numeric'],
            'items.*.length' => ['nullable', 'numeric'],
            'items.*.width' => ['nullable', 'numeric'],
        ]);

        $data = \Illuminate\Support\Arr::whereNotNull($validator->validated());

        $response     = Biteship::api()->get(self::$apiUri, $data);
        $responseJson = $response->json();

        return collect($responseJson['couriers'])->map(function (array $attributes) {
            return new static($attributes);
        });
    }
}
