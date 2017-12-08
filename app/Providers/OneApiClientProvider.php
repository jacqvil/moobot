<?php

namespace MooBot\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Moo\OneApi\OneApiClient;
use Moo\OneApi\OneApiClientInterface;

class OneApiClientProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OneApiClientInterface::class, function ($app) {
            $config = config('oneapi');

            $client = new Client([
                'base_uri' => $config['ONEAPI_URL'],
                "curl"        => [
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_TIMEOUT_MS => 30000,
                    CURLOPT_CONNECTTIMEOUT => 30000,
                ]
            ]);

            return new OneApiClient($client, $config);
        });
    }
}
