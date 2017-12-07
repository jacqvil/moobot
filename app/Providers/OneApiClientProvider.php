<?php

namespace MooBot\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use OneApi\OneApiClient;
use OneApi\OneApiClientInterface;

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
                'base_uri' => $config['ONEAPI_URL']
            ]);

            return new OneApiClient($client, $config);
        });
    }
}
