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
     * Search for areas by single search input
     *
     * @param string $input
     * @param string $countries
     * @return \Illuminate\Support\Collection<Area>
     *
     * @see https://biteship.com/id/docs/api/maps/retrieve_area_single
     */
    public static function search(string $input, string $countries = 'ID'): \Illuminate\Support\Collection
    {
        $params = compact('input', 'countries');

        $response     = Biteship::api()->get(self::$apiUri, $params);
        $responseJson = $response->json();

        return collect($responseJson['areas'])->map(function (array $attributes) {
            return new static($attributes);
        });
    }
}