<?php

namespace Kwaadpepper\CrudPolicies;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;

class CrudPoliciesServiceProvider extends ServiceProvider
{

    protected $commands = [];

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../../resources/lang/', 'omen');
        $this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'omen');

        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/crud-policies/')
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang/' => resource_path('lang/vendor/crud-policies/')
        ], 'lang');
    }

    public function register()
    {
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('CrudType', CrudType::class);
            $loader->alias('CrudAction', CrudAction::class);
        });
    }
}
