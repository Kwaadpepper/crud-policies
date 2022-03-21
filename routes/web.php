<?php

use Illuminate\Support\Facades\Route;
use Kwaadpepper\CrudPolicies\Http\Controllers\AssetsController;
use Kwaadpepper\CrudPolicies\Http\Controllers\ModelsController;
use Kwaadpepper\CrudPolicies\Http\Controllers\UploadController;

$middlewareMinimal = [
    \Illuminate\Routing\Middleware\SubstituteBindings::class
];

Route::group([
    'as' => 'crud-policies.httpFileSend.',
    'middleware' => array_merge(['throttle:60,1'], $middlewareMinimal),
    'namespace' => 'Kwaadpepper\CrudPolicies\Http\Controllers'
], function () {
    $routePrefix = config('crud-policies.urlPrefix');

    // Assets.
    Route::get(\sprintf('%s/{type}/{fileUri}', config('crud-policies.assetPath')), [AssetsController::class, 'asset'])
        ->where('type', '(js|css)')
        ->where('fileUri', '.*')
        ->name('asset')
        ->prefix($routePrefix);
});

Route::group([
    'as' => 'crud-policies.models.',
    'middleware' => array_merge(
        ['throttle:250,1'],
        $middlewareMinimal,
        config('crud-policies.middlewares')
    ),
    'namespace' => 'Kwaadpepper\CrudPolicies\Http\Controllers'
], function () {
    $routePrefix = config('crud-policies.urlPrefix');

    // Order change.
    Route::put('{modelTable}/{modelId}/{colName}/{newOrder}/{oldUrl}', [ModelsController::class, 'changeOrder'])
        ->where('modelTable', '[a-z_]*')
        ->where('modelId', '[0-9]*')
        ->where('colName', '[a-zA-Z0-9_]*')
        ->where('newOrder', '[0-9]*')
        ->name('changeOrder')
        ->prefix($routePrefix);
});

Route::group([
    'as' => 'crud-policies.',
    'middleware' => array_merge(
        config('crud-policies.middlewares'),
        ['throttle:20,1'],
        $middlewareMinimal,
        [\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class],
    ),
    'namespace' => 'Kwaadpepper\CrudPolicies\Http\Controllers'
], function () {
    $routePrefix = config('crud-policies.urlPrefix');

    // Upload image.
    Route::post('upload', [UploadController::class, 'uploadFile'])
        ->name('upload')
        ->prefix($routePrefix);
});
