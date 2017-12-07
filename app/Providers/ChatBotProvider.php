<?php

namespace MooBot\Providers;

use Illuminate\Support\ServiceProvider;
use Moo\ChatBot\ChatbotHelper;

class ChatBotProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ChatbotHelper::class, function ($app) {
            $config = config('chatbot');
            return new ChatbotHelper($config);
        });
    }
}
