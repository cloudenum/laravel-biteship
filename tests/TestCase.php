<?php

namespace Cloudenum\Biteship\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use Cloudenum\Biteship\BiteshipServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            BiteshipServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function mockApiResponse(array|string|null $body = null, int $status = 200)
    {
        Http::fake([
            '*' => Http::response($body, $status)
        ]);
    }
}
