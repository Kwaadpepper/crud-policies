<?php

namespace Kwaadpepper\CrudPolicies;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'crud-policies');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'crud-policies');
        $translations = collect();
        foreach ($this->collectLocalesStrings() as $locale) { // suported locales
            Cache::rememberForever(sprintf('crud-policies.translations.%s', $locale), function () use ($locale) {
                $translations = [
                    'php' => $this->phpTranslations($locale),
                    'json' => $this->jsonTranslations($locale),
                ];
                return $translations;
            });
        }

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
            $loader->alias('Str', Str::class);
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

    private function collectLocalesStrings()
    {
        return collect(File::allFiles(__DIR__ . '/../resources/lang'))->flatMap(function ($file) {
            if ($file->getRelativePath()) {
                return [$file->getRelativePath() => ''];
            }
        })->keys();
    }

    private function phpTranslations($locale)
    {
        $path = __DIR__ . "/../resources/lang/$locale";
        return collect(File::allFiles($path))->flatMap(function ($file) {
            $key = $file->getBasename('.php');
            return [$key => include $file];
        });
    }

    private function jsonTranslations($locale)
    {
        $path = "/../resources/lang/$locale.json";
        if (is_string($path) && is_readable($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return [];
    }
}
