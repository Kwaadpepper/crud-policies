<?php

namespace Kwaadpepper\CrudPolicies\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 *
 * https://laravel.com/docs/8.x/authorization
 * Controller Method    Policy Method
 * index                viewAny
 * show                 view
 * create               create
 * store                create
 * edit                 update
 * update               update
 * destroy              delete
 *
 * @method static self viewAny()
 * @method static self view()
 * @method static self create()
 * @method static self update()
 * @method static self delete()
 */
class CrudAction extends BaseEnumRoutable
{
    /**
     * Values
     *
     * @return array
     */
    protected static function values(): array
    {
        return [
            'viewAny' => 0,
            'view' => 1,
            'create' => 2,
            'update' => 3,
            'delete' => 4,
        ];
    }

    /**
     * Labels
     *
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'viewAny' => trans('crud-policies::crud.viewAny'),
            'view' => trans('crud-policies::crud.view'),
            'create' => trans('crud-policies::crud.create'),
            'update' => trans('crud-policies::crud.update'),
            'delete' => trans('crud-policies::crud.delete'),
        ];
    }
}
