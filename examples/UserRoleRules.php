<?php

namespace App\Policies\Rules;

use App\Enums\Role;
use App\Models\User;
use Kwaadpepper\CrudPolicies\Policies\Rules\StaticRules;

/**
 * Userlevel Policiy Static Rules
 *! Rules has to return boolean value
 */
abstract class UserRoleRules extends StaticRules
{

    protected static function userIsAdmin(User $user)
    {
        return $user->role->equals(Role::admin());
    }
}
