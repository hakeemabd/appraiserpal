<?php

namespace App\Providers;

use App\Repositories\SnippetRepository;
use Illuminate\Support\ServiceProvider;

class ContentSnippetsProvider extends ServiceProvider
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
        $this->app->singleton('snippetManager', function ($app) {
            return $app->make(SnippetRepository::class);
        });
    }
}
