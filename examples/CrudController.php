<?php

namespace App\Http\Controllers;

use App\Models\CrudModel;
use Kwaadpepper\CrudPolicies\Traits\CrudController;

class CrudController extends Controller
{
    use CrudController;

    /** @var CrudModel */
    public static $modelClass = CrudModel::class;
}
