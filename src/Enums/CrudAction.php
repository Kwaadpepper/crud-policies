<?php

namespace Kwaadpepper\CrudPolicies\Enums;

use Kwaadpepper\Enum\BaseEnum;

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

class CrudAction extends BaseEnum
{
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

    protected static function labels(): array
    {
        return [
            'viewAny' => trans('List'),
            'view' => trans('View'),
            'create' => trans('Create'),
            'update' => trans('Edit'),
            'delete' => trans('Delete'),
        ];
    }
}
