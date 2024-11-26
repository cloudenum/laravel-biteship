<?php

namespace Cloudenum\Biteship\Tests;

use Illuminate\Support\Facades\Http;

class BiteshipApiTest extends TestCase
{
    public function test_headers()
    {
        $config = [
            'api_key' => 'test',
            'base_url' => 'https://api.example.com',
        ];

        $api = new \Cloudenum\Biteship\BiteshipApi($config);

        $headers = $api->headers();

        $this->assertArrayHasKey('Accept', $headers);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals('test', $headers['Authorization']);
    }

    public function test_base_url_without_base_url_config()
    {
        $config = [
            'api_key' => 'test',
        ];

        $api = new \Cloudenum\Biteship\BiteshipApi($config);

        $url = $api->baseUrl('/endpoint');

        $this->assertEquals(\Cloudenum\Biteship\BiteshipApi::DEFAULT_BASE_URL.'/endpoint', $url);
    }

    public function test_base_url_with_base_url_config()
    {
        $config = [
            'api_key' => 'test',
            'base_url' => 'https://api.example.com',
        ];

        $api = new \Cloudenum\Biteship\BiteshipApi($config);

        $url = $api->baseUrl('/endpoint');

        $this->assertEquals('https://api.example.com/endpoint', $url);
    }

    public function test_request_success()
    {
        Http::fake([
            '*' => Http::response(['status' => true]),
        ]);

        $config = [
            'api_key' => 'test',
        ];

        $api = new \Cloudenum\Biteship\BiteshipApi($config);

        $response = $api->request('get', '/endpoint');

        $this->assertEquals(200, $response->status());
        $this->assertEquals('success', $response->json('status'));
    }

    public function test_request_failure()
    {
        Http::fake([
            '*' => Http::response(['status' => false], 500),
        ]);

        $config = [
            'api_key' => 'test',
        ];

        $api = new \Cloudenum\Biteship\BiteshipApi($config);

        $this->expectException(\Cloudenum\Biteship\Exceptions\RequestException::class);

        $api->request('get', '/endpoint');
    }

    public function test_invalid_configs()
    {
        $config = [
            'api_key' => null,
        ];

        $this->expectException(\Cloudenum\Biteship\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key must be provided');
        new \Cloudenum\Biteship\BiteshipApi($config);

        $config = [
            'api_key' => 123,
        ];

        $this->expectException(\Cloudenum\Biteship\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key must be a string');
        new \Cloudenum\Biteship\BiteshipApi($config);

        $config = [
            'api_key' => 'test',
            'base_url' => 123,
        ];

        $this->expectException(\Cloudenum\Biteship\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('base_url must be a string');
        new \Cloudenum\Biteship\BiteshipApi($config);

        $config = [
            'api_key' => 'test',
            'base_url' => 'invalid-url',
        ];

        $this->expectException(\Cloudenum\Biteship\Exceptions\InvalidArgumentException::class);
        $this->expectExceptionMessage('base_url is not a valid URL');
        new \Cloudenum\Biteship\BiteshipApi($config);
    }
}
