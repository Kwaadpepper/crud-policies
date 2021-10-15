<?php

namespace Kwaadpepper\CrudPolicies\Http\Controllers;

use Illuminate\Routing\Controller;
use Kwaadpepper\CrudPolicies\Traits\CrudController;

class ModelsController extends Controller
{
    public function changeOrder(string $modelTable, int $modelId, string $colName, int $newOrder, string $oldUrl)
    {
        $oldUrl = \base64_decode(str_replace(array('-', '_'), array('+', '/'), $oldUrl));

        if (!$oldUrl) {
            abort(404);
        }
        $modelClass = null;
        foreach (CrudController::getCrudModels() as $className) {
            if ((new $className())->getTable() === $modelTable) {
                $modelClass = $className;
                break;
            }
        }
        if (!$modelClass) {
            abort(404);
        }
        $model = $modelClass::whereId($modelId)->firstOrFail();
        if (is_null($model->{$colName}) or !is_int($model->{$colName})) {
            abort(404);
        }
        if (
            $switchModel = $modelClass::where(
                $colName,
                ($model->{$colName} - $newOrder < 0) ? '=' : '>',
                $newOrder
            )->orderBy($colName)->first()
        ) {
            $switchModel->{$colName} = $model->{$colName};
            $switchModel->saveOrFail();
        }
        $model->{$colName} = $newOrder;
        $model->saveOrFail();
        CrudController::afterSave($model);
        return redirect()->to($oldUrl);
    }
}
