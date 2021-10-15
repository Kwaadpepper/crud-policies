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
        //on cherche le model qui doit échanger sa position avec notre model selectionné
        //si ($model->{$colName} - $newOrder < 0) alors on descend
        //alors on va chercher le premier model qui est après le model selectionné
        //si ($model->{$colName} - $newOrder > 0) alors on monte
        //alors on va chercher le premier model qui a l'order cible
        if (
            $switchModel = $modelClass::where(
                $colName,
                ($model->{$colName} - $newOrder < 0) ? '>' : '=',
                ($model->{$colName} - $newOrder < 0) ? $model->{$colName} : $newOrder
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
