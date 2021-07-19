<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Kwaadpepper\CrudPolicies\Policies\RootPolicy;
use App\Policies\Rules\UserStaticRules as USR;
use App\Policies\Rules\UserRoleRules as URR;
use App\Policies\Rules\EmployeeStaticRules as SaSR;
use Illuminate\Database\Eloquent\Model;
use Kwaadpepper\CrudPolicies\Policies\Rules\StaticRules;

class CrudPolicy extends RootPolicy
{
    use HandlesAuthorization;

    /**
     * Init policies on call
     *
     * @param User $user
     * @param Model $model
     * @return void
     */
    public function setPolicies(User $user, Model $model = null)
    {
        /**
         * This first rule to return true in a policy action authorises the action
         */
        $this->policies = [
            'employee' => [
                'viewAny' => USR::make('everyOne'),
                'view' => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model)
                ]),
                'update' => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model)
                ]),
                'create' => USR::make('everyOne'),
                'delete' => StaticRules::or([
                    URR::make('userIsAdmin', $user),
                    SaSR::make('employeeIsOwnedByUser', $user, $model)
                ])
            ]
        ];
    }
}
