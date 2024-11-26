# Unonfficial Laravel package for Biteship API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cloudenum/laravel-biteship.svg?style=flat-square)](https://packagist.org/packages/cloudenum/laravel-biteship)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/cloudenum/laravel-biteship/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/cloudenum/laravel-biteship/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/cloudenum/laravel-biteship/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/cloudenum/laravel-biteship/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/cloudenum/laravel-biteship.svg?style=flat-square)](https://packagist.org/packages/cloudenum/laravel-biteship)

With this package you can easily interact with [Biteship API](https://biteship.com/en/docs/intro)

Here is a simple example to retrieve shipping costs
```php
// First you must define the items to ship 
$items = [
    [
        'name' => 'Black L',
        'description' => 'White Shirt',
        'category' => 'fashion',
        'value' => 165000,
        'quantity' => 1,
        'height' => 10,
        'length' => 10,
        'weight' => 200,
        'width' => 10,
    ]
];

// Then specify the destination and the origin
// You could use Postal Code but Biteship recommends you to use 
// their's Area ID, because it is more accurate.
$destination = 12950;
$origin = 12440;
$availableCouriers = \Cloudenum\Biteship\Courier::all();

$rates = \Cloudenum\Biteship\CourierPricing::Rates([
    'origin_postal_code' => $origin,
    'destination_postal_code' => $destination,
    'couriers' => implode(',', $availableCouriers->pluck('courier_code')->unique()->toArray()),
    'items' => $items,
]);
```

## Installation

You can install the package via composer:

```bash
composer require cloudenum/laravel-biteship
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-biteship-config"
```

This is the contents of the published config file:

```php
return [
    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com'),
    'api_key' => env('BITESHIP_API_KEY'),
];
```

## Usage

The usage of this package will largely follows [Biteship API Usage Flow](https://biteship.com/id/docs/api/usage_flow).

### Authentication

You must obtain an API key to use this package. 
Biteship has documentation on how you could [get an API key](https://biteship.com/id/docs/api/authentication#generate-new-api-key).  

After you get your API key add below environments to your `.env` file
```properties
BITESHIP_API_KEY=<YourApiKey>
```

### Area

#### Retrieve Area With Single Search

[API Doc](https://biteship.com/id/docs/api/maps/retrieve_area_single)

Example:

```php
\Cloudenum\Biteship\Area::search("Lebak Bulus");
```

#### Retrieve Area With Double Search

[API Doc](https://biteship.com/id/docs/api/maps/retrieve_area_double)

For the first request you could use the `Area::search()` method and set the `double` paramater to `true`.  
Then for the second request you could use `Area::doubleSearchSecondRequest()` method.

Example:

```php
$area = \Cloudenum\Biteship\Area::search("Lebak Bulus")->first();

\Cloudenum\Biteship\Area::doubleSearchSecondRequest($area->id);
```

> ***Tip***:  
> You could get Area by their's ID with `Area::doubleSearchSecondRequest()` method.

### Rates

#### Retrieve Courier Rates

[API Doc](https://biteship.com/id/docs/api/rates/retrieve) 

Example:
```php
$items = [
    [
        'name' => 'Black L',
        'description' => 'White Shirt',
        'category' => 'fashion',
        'value' => 165000,
        'quantity' => 1,
        'height' => 10,
        'length' => 10,
        'weight' => 200,
        'width' => 10,
    ]
];

$destination = 55510;
$origin = 12440;
$availableCouriers = \Cloudenum\Biteship\Courier::all();

$rates = \Cloudenum\Biteship\CourierPricing::Rates([
    'origin_postal_code' => $origin,
    'destination_postal_code' => $destination,
    'couriers' => implode(',', $availableCouriers->pluck('courier_code')->unique()->toArray()),
    'items' => $items,
]);
```

### Shipping Order

#### Create a Shipping Order

[API Doc](https://biteship.com/id/docs/api/orders/create)

Example:

```php
$data = [
    'shipper_contact_name' => 'Amir',
    'shipper_contact_phone' => '088888888888',
    'shipper_contact_email' => 'biteship@test.com',
    'shipper_organization' => 'Biteship Org Test',
    'origin_contact_name' => 'Amir',
    'origin_contact_phone' => '088888888888',
    'origin_address' => 'Plaza Senayan, Jalan Asia Afrika',
    'origin_note' => 'Deket pintu masuk STC',
    'origin_postal_code' => 12440,
    'destination_contact_name' => 'John Doe',
    'destination_contact_phone' => '088888888888',
    'destination_address' => 'Lebak Bulus MRT',
    'destination_postal_code' => 12950,
    'destination_note' => 'Near the gas station',
    'courier_company' => 'jne',
    'courier_type' => 'reg',
    'courier_insurance' => 500000,
    'delivery_type' => 'now',
    'order_note' => 'Please be careful',
    'metadata' => [],
    'items' => [
        [
            'name' => 'Black L',
            'description' => 'White Shirt',
            'category' => 'fashion',
            'value' => 165000,
            'quantity' => 1,
            'height' => 10,
            'length' => 10,
            'weight' => 200,
            'width' => 10,
        ]
    ]
];

$biteshipOrder = \Cloudenum\Biteship\Order::create($data);
```

#### Get Shipping Order By ID

[API Doc]()

Example:

```php
$biteshipOrder = \Cloudenum\Biteship\Order::find("ID");
```

#### Cancel a Shipping Order

[API Doc](https://biteship.com/id/docs/api/orders/delete)

Example:

```php
// The reason could be a message why it is cancelled.
$biteshipOrder->cancel($reason);
```

### Shipment Tracking

#### Get Shipment Tracking By ID

[API Doc](https://biteship.com/id/docs/api/trackings/retrieve)

> ***Note***  
> Tracking ID is **not** Waybill ID issued by couriers

Example:

```php
$tracking = \Cloudenum\Biteship\Tracking::find("TrackingId");
```

#### Get Shipment Tracking By Waybill ID

[API Doc](https://biteship.com/id/docs/api/trackings/retrieve_public)

Example:

```php
$tracking = \Cloudenum\Biteship\Tracking::findPublicTracking("WaybillId");
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hammam Afiq Murtadho](https://github.com/cloudenum)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
