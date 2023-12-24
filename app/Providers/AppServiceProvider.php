<?php

namespace App\Providers;

use App\Exceptions\InvalidConfigException;
use App\Services\BenfordLawService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BenfordLawService::class, function (Application $app) {
           $benfordDistributionConfig = config('benford.distribution', []);
            if (empty($benfordDistributionConfig)) {
                throw new InvalidConfigException('Missing Benford distribution configuration array');
            }

            return new BenfordLawService($benfordDistributionConfig);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
