<?php

namespace Kwaadpepper\CrudPolicies\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Traits\CrudController;

class CrudPoliciesServiceProvider extends ServiceProvider
{

    protected const ROOTPATH = __DIR__ . '/../../';

    /** @var array */
    protected $commands = [];

    /**
     * CrudPoliciesServiceProvider boot
     *
     * @return void
     */
    public function boot()
    {
        require_once static::ROOTPATH . 'src/helpers.php';

        $this->loadRoutesFrom(static::ROOTPATH . 'routes/web.php');
        $this->loadViewsFrom(static::ROOTPATH . 'resources/views/', 'crud-policies');
        $this->loadTranslationsFrom(static::ROOTPATH . 'resources/lang/', 'crud-policies');
        foreach ($this->collectLocalesStrings() as $locale) {
            // * Suported locales
            Cache::rememberForever(sprintf('crud-policies.translations.%s', $locale), function () use ($locale) {
                return [
                    'php' => $this->phpTranslations($locale)->all(),
                    'json' => $this->jsonTranslations($locale)->all(),
                ];
            });
        }

        $this->publishes([
            static::ROOTPATH . 'config' => config_path(),
        ], 'config');

        $this->publishes([
            static::ROOTPATH . 'resources/views' => resource_path('views/vendor/crud-policies/')
        ], 'views');

        $this->publishes([
            static::ROOTPATH . 'resources/lang/' => resource_path('lang/vendor/crud-policies/')
        ], 'lang');

        $publicPath = config('crud-policies.assetPath');
        $this->publishes([
            static::ROOTPATH . 'resources/js' => resource_path($publicPath . '/js'),
            static::ROOTPATH . 'resources/sass' => resource_path($publicPath . '/css')
        ], 'assetsSource');

        $this->publishes([
            static::ROOTPATH . 'crud-policies/js' => public_path($publicPath . '/js'),
            static::ROOTPATH . 'crud-policies/css' => public_path($publicPath . '/css')
        ], 'assetsCompiled');

        // * Directives for assets
        Blade::directive(
            'crudPoliciesCSS',
            function () {
                return sprintf(
                    '<link rel="stylesheet" href=\'%s\'/>',
                    route('crud-policies.httpFileSend.asset', [
                        'type' => 'css',
                        'fileUri' => 'crud.css'
                    ])
                );
            }
        );
        Blade::directive(
            'crudPoliciesJS',
            function () {
                return sprintf(
                    '<script src=\'%s\'></script>',
                    route('crud-policies.httpFileSend.asset', [
                        'type' => 'js',
                        'fileUri' => 'crud.js'
                    ])
                );
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            // * Alias for Classes
            $loader->alias('Str', Str::class);
            $loader->alias('CrudType', CrudType::class);
            $loader->alias('CrudAction', CrudAction::class);
            $loader->alias('CrudController', CrudController::class);
            $this->mergeConfigFrom(
                static::ROOTPATH . 'config/crud-policies.php',
                'crud-policies'
            );
            $this->mergeConfigFrom(
                static::ROOTPATH . 'config/purifier.php',
                'purifier'
            );
        });
    }

    /**
     * Get all available locales name
     *
     * @return \Illuminate\Support\Collection
     */
    private function collectLocalesStrings(): \Illuminate\Support\Collection
    {
        return collect(File::allFiles(static::ROOTPATH . 'resources/lang'))
            ->flatMap(function ($file) {
                if ($file->getRelativePath()) {
                    return [$file->getRelativePath() => ''];
                }
            })->keys();
    }

    /**
     * Get all php translations
     *
     * @param string $locale
     * @return \Illuminate\Support\Collection
     */
    private function phpTranslations(string $locale): \Illuminate\Support\Collection
    {
        $path = static::ROOTPATH . "resources/lang/$locale";
        return collect(File::allFiles($path))->flatMap(function ($file) {
            $key = $file->getBasename('.php');
            return [$key => include $file];
        });
    }

    /**
     * Get all json translations
     *
     * @param string $locale
     * @return \Illuminate\Support\Collection
     */
    private function jsonTranslations(string $locale): \Illuminate\Support\Collection
    {
        $path = static::ROOTPATH . "resources/lang/$locale.json";
        if (is_string($path) && is_readable($path)) {
            return collect(json_decode(file_get_contents($path), true));
        }
        return collect([]);
    }
}
