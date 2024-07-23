<?php

namespace Cloudenum\Biteship;

use Illuminate\Support\Facades\Http;

/**
 * API class
 */
class BiteshipApi
{
    /**
     * The default base URL of the Biteship API
     * @var string
     */
    const DEFAULT_BASE_URL = 'https://api.biteship.com';

    /**
     * The base URL of the Biteship API
     */
    private string $baseUrl;

    /**
     * The API key
     */
    private ?string $apiKey = null;

    /**
     * The required headers
     */
    private array $requiredHeaders;

    public function __construct(array $config = [])
    {
        $this->validateConfig($config);

        $this->apiKey  = $config['api_key'] ?? null;
        $this->baseUrl = $config['base_url'] ?? static::DEFAULT_BASE_URL;

        $this->setRequiredHeaders();
    }

    /**
     * Validate the configuration
     * @param array $config
     * @return void
     *
     * @throws \Cloudenum\Biteship\Exceptions\InvalidArgumentException
     */
    private function validateConfig(array $config): void
    {
        if (empty($config['api_key'])) {
            throw new \Cloudenum\Biteship\Exceptions\InvalidArgumentException('api_key must be provided');
        }

        if (!is_string($config['api_key'])) {
            throw new \Cloudenum\Biteship\Exceptions\InvalidArgumentException('api_key must be a string');
        }

        if (isset($config['base_url']) && !is_string($config['base_url'])) {
            throw new \Cloudenum\Biteship\Exceptions\InvalidArgumentException('base_url must be a string');
        }

        if (isset($config['base_url']) && !filter_var($config['base_url'], FILTER_VALIDATE_URL)) {
            throw new \Cloudenum\Biteship\Exceptions\InvalidArgumentException('base_url is not a valid URL');
        }
    }

    /**
     * Set the required headers
     */
    private function setRequiredHeaders(): void
    {
        $this->requiredHeaders = [
            'Accept' => 'application/json',
            'Authorization' => $this->apiKey,
        ];
    }

    public function headers(array $headers = []): array
    {
        return array_merge($this->requiredHeaders, $headers);
    }

    public function baseUrl(string $uri): string
    {
        if (!$this->baseUrl) {
            return $uri;
        }

        return ltrim($this->baseUrl, '/') . '/' . trim($uri, '/');
    }

    /**
     * Make a request to the Biteship API
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Cloudenum\Biteship\Exceptions\RequestException
     */
    public function request(string $method, string $url, array $data = [], array $headers = [])
    {
        /** @var \Illuminate\Http\Client\Response */
        $response = Http::withHeaders($this->headers($headers))
            ->$method($this->baseUrl($url), $data);

        if ($response->failed()) {
            throw new \Cloudenum\Biteship\Exceptions\RequestException($response);
        }

        return $response;
    }

    /**
     * Make a GET request to the Biteship API
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Cloudenum\Biteship\Exceptions\RequestException
     */
    public function get(string $url, array $data = [], array $headers = [])
    {
        return $this->request('get', $url, $data, $headers);
    }

    /**
     * Make a POST request to the Biteship API
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Cloudenum\Biteship\Exceptions\RequestException
     */
    public function post(string $url, array $data = [], array $headers = [])
    {
        return $this->request('post', $url, $data, $headers);
    }

    /**
     * Make a PUT request to the Biteship API
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Cloudenum\Biteship\Exceptions\RequestException
     */
    public function put(string $url, array $data = [], array $headers = [])
    {
        return $this->request('put', $url, $data, $headers);
    }

    /**
     * Make a DELETE request to the Biteship API
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Cloudenum\Biteship\Exceptions\RequestException
     */
    public function delete(string $url, array $data = [], array $headers = [])
    {
        return $this->request('delete', $url, $data, $headers);
    }
}
