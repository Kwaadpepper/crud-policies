<?php

namespace Kwaadpepper\CrudPolicies\Http\Requests;

use Kwaadpepper\CrudPolicies\Enums\CrudAction;

class StoreCrudModel extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        $authorized = auth()->check();
        if ($authorized) {
            $this->rules = $this->getRulesForAction(CrudAction::create());
        }
        return $authorized;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }
}
