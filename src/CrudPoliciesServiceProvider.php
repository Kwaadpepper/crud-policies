<?php

namespace Kwaadpepper\CrudPolicies;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Traits\CrudController;

class CrudPoliciesServiceProvider extends ServiceProvider
{

    protected $commands = [];

    public function boot()
    {
        $this->configLoadIfNeeded();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'crud-policies');
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'crud-policies');

        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/crud-policies/')
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang/' => resource_path('lang/vendor/crud-policies/')
        ], 'lang');

        $publicPath = config('crud-policies.assetPath');
        $this->publishes([
            __DIR__ . '/../resources/js' => public_path($publicPath . '/js'),
            __DIR__ . '/../resources/css' => public_path($publicPath . '/css')
        ], 'assets');
    }

    public function register()
    {
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            // Alias for Classes
            $loader->alias('CrudType', CrudType::class);
            $loader->alias('CrudAction', CrudAction::class);
            $loader->alias('CrudController', CrudController::class);
        });
    }

    private function configLoadIfNeeded()
    {
        if (!config('crud-policies.isLoaded')) {
            $config = require __DIR__ . '/../config/crud-policies.php';
            config(['crud-policies' => $config]);
        }
    }
}
