<?php

namespace Anderson9527\LaravelDevHelper;

use Anderson9527\LaravelDevHelper\Commands\MakeDiffContents;
use Anderson9527\LaravelDevHelper\Commands\MakeModelProperties;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelDevHelperServiceProvider
 */
class LaravelDevHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModelProperties::class,
                MakeDiffContents::class,
            ]);
            // 發佈「本機用」設定檔範本到專案根目錄
            // php artisan vendor:publish --tag=local-anderson9527-laravel-dev-helper
            // php artisan vendor:publish --tag=local-anderson9527-laravel-dev-helper --force
            $this->publishes(
                [
                    __DIR__ . '/stubs/local-anderson9527-laravel-dev-helper.php' => base_path('.local-anderson9527-laravel-dev-helper.php'),
                ],
                'local-anderson9527-laravel-dev-helper'
            );
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 若你未來有需要 publish config 或 bind service，可以在這裡做
    }
}
