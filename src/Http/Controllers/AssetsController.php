<?php

namespace Kwaadpepper\CrudPolicies\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class AssetsController extends Controller
{
    public function asset(string $type, string $fileUri)
    {
        $data = null;
        try {
            $data = File::get(__DIR__ . "/../../../crud-policies/$type/$fileUri");
        } catch (FileNotFoundException $e) {
            abort(404);
        }
        return response($data, 200, [
            'Content-Type' => $type === 'js' ? 'text/javascript' : 'text/css'
        ]);
    }
}
