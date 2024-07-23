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
        $validator = validator($data, [
            'shipper_contact_name' => ['nullable', 'string'],
            'shipper_contact_phone' => ['nullable', 'string'],
            'shipper_contact_email' => ['nullable', 'string', 'email'],
            'shipper_organization' => ['nullable', 'string'],
            'origin_contact_name' => ['required', 'string'],
            'origin_contact_phone' => ['required', 'string'],
            'origin_contact_email' => ['nullable', 'string', 'email'],
            'origin_address' => ['required', 'string'],
            'origin_postal_code' => ['required_without:origin_coordinate,origin_area_id', 'integer'],
            'origin_coordinate' => ['required_without:origin_postal_code,origin_area_id', 'array'],
            'origin_coordinate.latitude' => ['required_with:origin_coordinate', 'numeric', 'min:-90', 'max:90'],
            'origin_coordinate.longitude' => ['required_with:origin_coordinate', 'numeric', 'min:-180', 'max:180'],
            'origin_area_id' => ['required_without:origin_postal_code,origin_coordinate', 'string'],
            'origin_location_id' => ['nullable', 'string'],
            'origin_collection_method' => ['nullable', 'in:pickup,drop_off'],
            'destination_contact_name' => ['required', 'string'],
            'destination_contact_phone' => ['required', 'string'],
            'destination_contact_email' => ['nullable', 'string', 'email'],
            'destination_address' => ['required', 'string'],
            'destination_note' => ['nullable', 'string'],
            'destination_postal_code' => ['required_without:origin_coordinate,origin_area_id', 'integer'],
            'destination_coordinate' => ['required_without:origin_postal_code,origin_area_id', 'array'],
            'destination_coordinate.latitude' => ['required_with:origin_coordinate', 'numeric', 'min:-90', 'max:90'],
            'destination_coordinate.longitude' => ['required_with:origin_coordinate', 'numeric', 'min:-180', 'max:180'],
            'destination_area_id' => ['required_without:origin_postal_code,origin_coordinate', 'string'],
            'destination_location_id' => ['nullable', 'string'],
            'destination_cash_on_delivery' => ['nullable', 'integer'],
            'destination_proof_of_delivery' => ['nullable', 'boolean'],
            'destination_proof_of_delivery_note' => ['required_with:destination_proof_of_delivery', 'string'],
            'courier_company' => ['required', 'string'],
            'courier_type' => ['required', 'string'],
            'courier_insurance' => ['nullable', 'integer'],
            'delivery_type' => ['required', 'string'],
            'delivery_date' => ['nullable', 'date_format:Y-m-d'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'order_note' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'reference_id' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
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
    public static function update(Order|string $id, array $data): static
    {
        $order = $id;
        if (is_string($order)) {
            $order = new static(['id' => $id]);
        }

        $validator = validator($data, [
            'shipper_contact_name' => ['string'],
            'shipper_contact_phone' => ['string'],
            'shipper_contact_email' => ['string', 'email'],
            'shipper_organization' => ['string'],
            'origin_contact_name' => ['string'],
            'origin_contact_phone' => ['string'],
            'origin_contact_email' => ['string', 'email'],
            'origin_address' => ['string'],
            'origin_postal_code' => ['prohibits:origin_coordinate,origin_area_id', 'integer'],
            'origin_coordinate' => ['prohibits:origin_postal_code,origin_area_id', 'array'],
            'origin_coordinate.latitude' => ['required_with:origin_coordinate', 'numeric', 'min:-90', 'max:90'],
            'origin_coordinate.longitude' => ['required_with:origin_coordinate', 'numeric', 'min:-180', 'max:180'],
            'origin_area_id' => ['prohibits:origin_postal_code,origin_coordinate', 'string'],
            'origin_location_id' => ['string'],
            'origin_collection_method' => ['in:pickup,drop_off'],
            'destination_contact_name' => ['string'],
            'destination_contact_phone' => ['string'],
            'destination_contact_email' => ['string', 'email'],
            'destination_address' => ['string'],
            'destination_note' => ['string'],
            'destination_postal_code' => ['prohibits:origin_coordinate,origin_area_id', 'integer'],
            'destination_coordinate' => ['prohibits:origin_postal_code,origin_area_id', 'array'],
            'destination_coordinate.latitude' => ['required_with:origin_coordinate', 'numeric', 'min:-90', 'max:90'],
            'destination_coordinate.longitude' => ['required_with:origin_coordinate', 'numeric', 'min:-180', 'max:180'],
            'destination_area_id' => ['prohibits:origin_postal_code,origin_coordinate', 'string'],
            'destination_location_id' => ['string'],
            'destination_cash_on_delivery' => ['integer'],
            'destination_proof_of_delivery' => ['boolean'],
            'destination_proof_of_delivery_note' => ['required_with:destination_proof_of_delivery', 'string'],
            'courier_company' => ['string'],
            'courier_type' => ['string'],
            'courier_insurance' => ['integer'],
            'delivery_type' => ['string'],
            'delivery_date' => ['date_format:Y-m-d'],
            'delivery_time' => ['date_format:H:i'],
            'order_note' => ['string'],
            'metadata' => ['array'],
            'reference_id' => ['string'],
            'tags' => ['array'],
            'tags.*' => ['string'],
            'items' => ['array'],
            'items.*.name' => ['required', 'string'],
            'items.*.description' => ['string'],
            'items.*.sku' => ['string'],
            'items.*.value' => ['required', 'numeric'],
            'items.*.quantity' => ['required', 'integer'],
            'items.*.weight' => ['required', 'numeric'],
            'items.*.height' => ['numeric'],
            'items.*.length' => ['numeric'],
            'items.*.width' => ['numeric'],
        ]);

        $data = \Illuminate\Support\Arr::whereNotNull($validator->validated());

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
