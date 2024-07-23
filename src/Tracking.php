<?php

namespace Cloudenum\Biteship;

/**
 * @property string|null $id
 * @property string|null $waybill_id
 * @property array|null $courier
 * @property array|null $origin
 * @property array|null $destination
 * @property array|null $history
 * @property string|null $link
 * @property string|null $order_id
 * @property string|null $status
 */
class Tracking extends BiteshipObject
{
    protected static string $apiUri = '/v1/trackings';

    protected array $dynamicProperties = [
        'id',
        'waybill_id',
        'courier',
        'origin',
        'destination',
        'history',
        'link',
        'order_id',
        'status',
    ];

    /**
     * Get the tracking details using tracking_id. This endpoint can only be used when you order via our order API. Biteship will generate tracking_id separately if you create an Order through Biteship API.
     *
     * ***Note***: The tracking_id is ***not*** the waybill id issued by couriers. If you want to find tracking data using waybill id use `Tracking::findPublicTracking()`
     *
     * @param  string  $id  The Tracking ID
     *
     * @see https://biteship.com/id/docs/api/trackings/retrieve
     */
    public static function find(string $id): static
    {
        $response = Biteship::api()->get(self::$apiUri."/$id");
        $responseJson = $response->json();

        return new static($responseJson);
    }

    /**
     * Get the tracking details using waybill id.
     * This endpoint can be used when you have the waybill id issued by couriers.
     *
     *
     * @see https://biteship.com/id/docs/api/trackings/retrieve_public
     */
    public static function findPublicTracking(string $waybillId, string $courierCode): static
    {
        $response = Biteship::api()->get(self::$apiUri."/$waybillId/couriers/$courierCode");
        $responseJson = $response->json();

        return new static($responseJson);
    }
}
