<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        //\Vite::macro('logo', fn (string $asset) => $this->asset("resources/js/src/assets/images/logo/{$asset}"));
        //\Vite::macro('logoContent', fn (string $asset) => file_get_contents($this->asset("resources/js/src/assets/images/logo/{$asset}"), false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]])));
        if (env('APP_PROD') == 'production') {
            \Vite::useBuildDirectory('dist');
        }
        //Vite::prefetch(concurrency: 3);
    }
}
