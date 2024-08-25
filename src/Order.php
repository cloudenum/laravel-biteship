<?php

namespace Cloudenum\Biteship;

/**
 * Order Model
 *
 * @property ?string $id
 * @property ?string $short_id
 * @property ?array $shipper name, email, phone, organization
 * @property ?array $origin
 * @property ?array $destination
 * @property ?array $delivery datetime, note, type, distance, distance_unit
 * @property ?array $courier tracking_id, waybill_id, company, driver_name, driver_phone, driver_photo_url, driver_plate_number, type, link, insurance, routing_code
 * @property ?array $voucher id, name, value, type
 * @property ?string $reference_id
 * @property ?string $invoice_id
 * @property ?array $items
 * @property ?array $metadata
 * @property ?array $tags
 * @property ?string $note
 * @property ?float $price
 * @property ?string $status
 * @property ?string $ticket_status
 * @property ?string $cancellation_reason
 *
 * @see https://biteship.com/id/docs/api/orders/overview
 */
class Order extends BiteshipObject
{
    protected static string $apiUri = '/v1/orders';

    protected array $dynamicProperties = [
        'id',
        'short_id',
        'shipper',
        'origin',
        'destination',
        'delivery',
        'courier',
        'voucher',
        'reference_id',
        'invoice_id',
        'items',
        'extra',
        'metadata',
        'tags',
        'note',
        'price',
        'status',
        'ticket_status',
    ];

    /**
     * Create a new Order
     *
     * @param  array  $data  The details on what are the parameters is in the API documentation.
     *
     * @see https://biteship.com/id/docs/api/orders/create
     */
    public static function create(array $data): static
    {
        $data = \Illuminate\Support\Arr::whereNotNull($data);

        $response     = Biteship::api()->post(self::$apiUri, $data);
        $responseJson = $response->json();

        return new static($responseJson ?? []);
    }

    public static function find(string $id)
    {
        $response     = Biteship::api()->get(self::$apiUri . '/' . $id);
        $responseJson = $response->json();

        return new static($responseJson);
    }

    /**
     * Update any changes to the Order
     *
     * @param  Order|string  $id  The Order ID or the Order object
     * @param  array  $data  The details on what are the parameters is in the API documentation
     *
     * @see https://biteship.com/id/docs/api/orders/update
     */
    public static function update(Order|string $id, array $data): \Cloudenum\Biteship\Order
    {
        $order = $id;
        if (is_string($order)) {
            $order = new static(['id' => $id]);
        }

        $data = \Illuminate\Support\Arr::whereNotNull($data);

        $response     = Biteship::api()->post(self::$apiUri . '/' . $order->id, $data);
        $responseJson = $response->json();

        if ($responseJson['success'] ?? false) {
            $order->fillDynamicProperties($responseJson);
        }

        return $order;
    }

    /**
     * Cancel the Order
     *
     * @param  string  $reason  The reason why the order is canceled
     *
     * @see https://biteship.com/id/docs/api/orders/delete
     */
    public function cancel(string $reason): bool
    {
        $data = [
            'cancellation_reason' => $reason,
        ];

        $success      = false;
        $response     = Biteship::api()->delete(self::$apiUri . '/' . $this->id, $data);
        $responseJson = $response->json();

        $success = $responseJson['success'] ?? false;

        if ($success) {
            $this->fillDynamicProperties($responseJson);
        }

        return $success;
    }
}
