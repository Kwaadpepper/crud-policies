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
            'belongsTo' => 10,
            'belongsToMany' => 11
        ];
    }

    protected static function labels(): array
    {
        return [
            'boolean' => trans('Boolean'),
            'int' => trans('Number'),
            'string' => trans('Simple string'),
            'text' => trans('Text'),
            'json' => trans('Json'),
            'image' => trans('Image'),
            'email' => trans('Email string'),
            'password' => trans('Password'),
            'float' => trans('Float number'),
            'enum' => trans('Enumeration'),
            'belongsTo' => trans('Foreign key'),
            'belongsToMany' => trans('Belongs To Many relation')
        ];
    }
}
