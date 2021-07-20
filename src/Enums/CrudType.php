<?php

namespace Kwaadpepper\CrudPolicies\Enums;

use Kwaadpepper\Enum\BaseEnum;

/**
 * @method static self boolean()
 * @method static self int()
 * @method static self string()
 * @method static self text()
 * @method static self json()
 * @method static self image()
 * @method static self email()
 * @method static self password()
 * @method static self float()
 * @method static self enum()
 * @method static self order()
 * @method static self color()
 * @method static self belongsTo()
 * @method static self belongsToMany()
 */

class CrudType extends BaseEnum
{
    protected static function values(): array
    {
        return [
            'boolean' => 0,
            'int' => 1,
            'string' => 2,
            'text' => 3,
            'json' => 4,
            'image' => 5,
            'email' => 6,
            'password' => 7,
            'float' => 8,
            'enum' => 9,
            'order' => 10,
            'color' => 11,
            'belongsTo' => 21,
            'belongsToMany' => 22
        ];
    }

    protected static function labels(): array
    {
        return [
            'boolean' => trans('crud-policies::crud.boolean'),
            'int' => trans('crud-policies::crud.int'),
            'string' => trans('crud-policies::crud.string'),
            'text' => trans('crud-policies::crud.text'),
            'json' => trans('crud-policies::crud.json'),
            'image' => trans('crud-policies::crud.image'),
            'email' => trans('crud-policies::crud.email'),
            'password' => trans('crud-policies::crud.password'),
            'float' => trans('crud-policies::crud.float'),
            'enum' => trans('crud-policies::crud.enum'),
            'order' => trans('crud-policies::crud.order'),
            'color' => trans('crud-policies::crud.color'),
            'belongsTo' => trans('crud-policies::crud.belongsTo'),
            'belongsToMany' => trans('crud-policies::crud.belongsToMany')
        ];
    }
}
