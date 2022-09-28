<?php

namespace Kwaadpepper\CrudPolicies\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class AssetsController extends Controller
{
    /**
     * Serve package assets
     *
     * @param string $type
     * @param string $fileUri
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function asset(string $type, string $fileUri)
    {
        $data = null;
        try {
            $data = File::get(__DIR__ . "/../../../crud-policies/$type/$fileUri");
        } catch (FileNotFoundException $e) {
            abort(404);
        }
        $responseArray = [
            'Content-Type' => $type === 'js' ? 'text/javascript' : 'text/css',
            'Cache-Control' => 'private, max-age=86400'
        ];
        return response($data, 200, $responseArray);
    }
}
