<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use App\Models\Auth\PersonalAccessToken;
use App\Rules\Boolean;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Sanctum::authenticateAccessTokensUsing(
            static function (PersonalAccessToken $accessToken, bool $is_valid) {
                $token = clone $accessToken;
                if ($token->expires_at) {
                    if ($token->expires_at->isPast()) {
                        $accessToken->delete();
                    }
                }

                return $token->expires_at ? $is_valid && ! $token->expires_at->isPast() : $is_valid;
            }
        );
        \Vite::macro('logo', fn (string $asset) => $this->asset("resources/js/assets/images/logo/{$asset}"));
        \Vite::macro('logoContent', fn (string $asset) => file_get_contents($this->asset("resources/js/assets/images/logo/{$asset}"), false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]])));
        if (env('APP_PROD') == 'production') {
            \Vite::useBuildDirectory('dist');
        }
        Validator::extend('booleable', [Boolean::class, 'legacyValidation'], __('validation.boolean'));
    }
}
