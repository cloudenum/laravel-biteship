<?php

namespace Cloudenum\Biteship;

/**
 * @property string|null $id
 * @property string|null $name
 * @property string|null $country_name
 * @property string|null $country_code
 * @property string|null $administrative_division_level_1_name
 * @property string|null $administrative_division_level_1_type
 * @property string|null $administrative_division_level_2_name
 * @property string|null $administrative_division_level_2_type
 * @property string|null $administrative_division_level_3_name
 * @property string|null $administrative_division_level_3_type
 * @property string|null $administrative_division_level_4_name
 * @property string|null $administrative_division_level_4_type
 * @property string|null $postal_code
 */
class Area extends BiteshipObject
{
    protected static string $apiUri = '/v1/maps/areas';

    protected array $dynamicProperties = [
        'id',
        'name',
        'country_name',
        'country_code',
        'administrative_division_level_1_name',
        'administrative_division_level_1_type',
        'administrative_division_level_2_name',
        'administrative_division_level_2_type',
        'administrative_division_level_3_name',
        'administrative_division_level_3_type',
        'administrative_division_level_4_name',
        'administrative_division_level_4_type',
        'postal_code',
    ];

    /**
     * Search for areas
     *
     * @param  string  $input  The search input
     * @param  string  $countries  The country code
     * @param  bool  $double  True for double search, single search otherwise
     * @return \Illuminate\Support\Collection<Area>
     *
     * @see https://biteship.com/id/docs/api/maps/retrieve_area_single
     */
    public static function search(string $input, string $countries = 'ID', bool $double = false): \Illuminate\Support\Collection
    {
        $params = [
            'input' => $input,
            'countries' => $countries,
            'type' => $double ? 'double' : 'single',
        ];

        $response = Biteship::api()->get(self::$apiUri, $params);
        $responseJson = $response->json();

        return collect($responseJson['areas'])->map(function (array $attributes) {
            return new static($attributes);
        });
    }

    /**
     * Search for areas by double search input.
     *
     * @param  string  $id  The area id from previous search result
     * @return \Illuminate\Support\Collection<Area>
     */
    public static function doubleSearchSecondRequest(string $id): \Illuminate\Support\Collection
    {
        $response = Biteship::api()->get(self::$apiUri.'/'.$id);
        $responseJson = $response->json();

        return collect($responseJson['areas'])->map(function (array $attributes) {
            return new static($attributes);
        });
    }
}
