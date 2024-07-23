<?php

namespace Cloudenum\Biteship;

/**
 * @property string|null $id
 * @property string|null $short_id
 * @property array|null $shipper name, email, phone, organization
 * @property array|null $origin
 * @property array|null $destination
 * @property array|null $delivery datetime, note, type, distance, distance_unit
 * @property array|null $voucher id, name, value, type
 * @property string|null $reference_id
 * @property string|null $invoice_id
 * @property array|null $items
 * @property array|null $metadata
 * @property array|null $tags
 * @property string|null $note
 * @property float|null $price
 * @property string|null $status
 * @property string|null $ticket_status
 * @property string|null $cancellation_reason
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

        $response = Biteship::api()->post(self::$apiUri, $data);
        $responseJson = $response->json();

        return new static($responseJson ?? []);
    }

    public static function find(string $id)
    {
        $response = Biteship::api()->get(self::$apiUri.'/'.$id);
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

        $response = Biteship::api()->post(self::$apiUri.'/'.$order->id, $data);
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

        $success = false;
        $response = Biteship::api()->delete(self::$apiUri.'/'.$this->id, $data);
        $responseJson = $response->json();

        $success = $responseJson['success'] ?? false;

        if ($success) {
            $this->fillDynamicProperties($responseJson);
        }

        return $success;
    }
}
