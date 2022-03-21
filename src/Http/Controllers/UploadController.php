<?php

namespace Kwaadpepper\CrudPolicies\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kwaadpepper\CrudPolicies\Http\Requests\UploadFileRequest;

class UploadController extends Controller
{
    /**
     * Provide upload file route as ajax.
     *
     * @param UploadFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(UploadFileRequest $request)
    {
        /** @var \Illuminate\Http\UploadedFile $image */
        $image   = $request->upload;
        $newPath = $this->prepareMoveFile($image->getClientOriginalName(), 'uploads');
        $newPath = str_replace(storage_path('app'), '', $newPath);
        $image->storePubliclyAs(pathinfo($newPath, \PATHINFO_DIRNAME), pathinfo($newPath, \PATHINFO_BASENAME));
        return response()->json([
            'url' => '/storage' . str_replace('/public', '', $newPath)
        ]);
    }

    /**
     * Prepares upload directory
     *
     * @param string $filename
     * @param string $folder
     * @return string
     */
    protected function prepareMoveFile(string $filename, string $folder): string
    {
        $date       = date('Y-m-d');
        $pathFolder = \storage_path("app/public/images/crud-policies/$folder/$date");
        if (!File::exists($pathFolder)) {
            File::makeDirectory($pathFolder, 0755, true);
        }
        $filename = sprintf('%s-%s', Str::uuid(), $filename);
        $fullPath = sprintf('%s/%s', $pathFolder, $filename);
        if (File::exists($fullPath)) {
            // This should never happen since were are using uuid in the path.
            File::delete($fullPath);
        }
        return $fullPath;
    }
}
