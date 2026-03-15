<?php

declare(strict_types=1);

namespace Nikba\LaravelMaib;

use Illuminate\Support\ServiceProvider;
use Nikba\LaravelMaib\Auth\MaibAuthService;
use Nikba\LaravelMaib\Auth\TokenCache;
use Nikba\LaravelMaib\Callback\SignatureValidator;
use Nikba\LaravelMaib\Http\MaibHttpClient;
use Nikba\LaravelMaib\Services\DeleteCardService;
use Nikba\LaravelMaib\Services\DirectPaymentService;
use Nikba\LaravelMaib\Services\OneclickPaymentService;
use Nikba\LaravelMaib\Services\PayInfoService;
use Nikba\LaravelMaib\Services\RecurringPaymentService;
use Nikba\LaravelMaib\Services\RefundService;
use Nikba\LaravelMaib\Services\TwoStepPaymentService;

/**
 * Laravel service provider for maib e-Commerce API integration.
 *
 * @author Nicolai Bargan <office@nikba.com>
 * @author Nikba Creative Studio <https://nikba.com>
 */
class MaibServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/maib.php', 'maib');

        $this->app->singleton(TokenCache::class, function ($app) {
            $driver = config('maib.cache_driver');
            $cache = $driver
                ? $app['cache']->store($driver)
                : $app['cache']->store();

            return new TokenCache(
                cache: $cache,
                prefix: config('maib.cache_prefix', 'maib_token'),
            );
        });

        $this->app->singleton(MaibAuthService::class, function ($app) {
            return new MaibAuthService(
                tokenCache: $app->make(TokenCache::class),
                projectId: config('maib.project_id', ''),
                projectSecret: config('maib.project_secret', ''),
                baseUrl: config('maib.base_url'),
            );
        });

        $this->app->singleton(MaibHttpClient::class, function ($app) {
            return new MaibHttpClient(
                auth: $app->make(MaibAuthService::class),
                baseUrl: config('maib.base_url'),
            );
        });

        $this->app->singleton(SignatureValidator::class, function () {
            return new SignatureValidator(
                signatureKey: config('maib.signature_key', ''),
            );
        });

        foreach ([
            DirectPaymentService::class,
            TwoStepPaymentService::class,
            RecurringPaymentService::class,
            OneclickPaymentService::class,
            RefundService::class,
            PayInfoService::class,
            DeleteCardService::class,
        ] as $service) {
            $this->app->singleton($service, fn ($app) => new $service(
                $app->make(MaibHttpClient::class)
            ));
        }

        $this->app->singleton('maib', function ($app) {
            return new MaibManager($app);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/maib.php' => config_path('maib.php'),
        ], 'maib-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/maib.php');
    }
}
