<?php

use Illuminate\Support\Facades\Route;

$middlewareMinimal = [
    \Illuminate\Routing\Middleware\SubstituteBindings::class
];


/**
 * Check CSRF cookie only for assets
 * the csrf token is validated in download controller
 * only if the inode is private
 */
Route::group([
    'as' => 'crud-policies.httpFileSend.',
    'middleware' => array_merge(['throttle:60,1'], $middlewareMinimal),
    'namespace' => 'Kwaadpepper\CrudPolicies\Http\Controllers'
], function () {
    $routePrefix = config('crud-policies.urlPrefix');

    // Assets
    Route::get(\sprintf('%s/{type}/{fileUri}', config('crud-policies.assetPath')), 'AssetsController@asset')
        ->where('type', '(js|css)')->where('fileUri', '.*')->name('asset');
});
