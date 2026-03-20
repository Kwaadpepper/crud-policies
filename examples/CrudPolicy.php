<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Rules\EmployeeStaticRules as SaSR;
use App\Policies\Rules\UserRoleRules as URR;
use App\Policies\Rules\UserStaticRules as USR;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Kwaadpepper\CrudPolicies\Policies\RootPolicy;
use Kwaadpepper\CrudPolicies\Policies\Rules\StaticRules;

class CrudPolicy extends RootPolicy
{
    use HandlesAuthorization;

    /**
     * Init policies on call
     *
     * @return void
     */
    public function setPolicies(User $user, ?Model $model = null)
    {
        /**
         * This first rule to return true in a policy action authorises the action
         */
        $this->policies = [
            'employee' => [
                'viewAny' => USR::make('everyOne'),
                'view'    => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model),
                ]),
                'update' => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model),
                ]),
                'create' => USR::make('everyOne'),
                'delete' => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model),
                ]),
            ],
        ];
    }
}
