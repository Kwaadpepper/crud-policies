<?php

namespace Kwaadpepper\CrudPolicies\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Exceptions\CrudException;
use Kwaadpepper\CrudPolicies\Traits\CrudController;

class CrudRequest extends FormRequest
{

    /** @var array */
    protected $rules = [];

    /**
     * Get rules for a specific action
     *
     * @param CrudAction $action
     * @return array
     */
    public function getRulesForAction(CrudAction $action): array
    {
        $props = $this->getCurrentModel()::getEditableProperties();
        $propsRules = [];
        foreach ($this->getCurrentModel()::getRules() as $k => $rules) {
            $propsRules[$k] = [];
            foreach ($rules as $rule) {
                $this->handleRule($action, $props[$k], $rule, $k, $propsRules[$k]);
            }
        }
        return $propsRules;
    }

    private function handleRule(CrudAction $action, array $prop, $rule, $ruleName, &$out)
    {
        $model = $this->getCurrentModel();
        $propIsAllowedOnUpdateAction = in_array($action, $prop['actions']);

        // dont copy required if prop is not allowed on update
        if (!$propIsAllowedOnUpdateAction and is_string($rule) and $rule == 'required') {
            return;
        }
        if (!in_array($rule, $out)) {
            $out[] = $rule;
        }
        // handle password
        if (
            $prop['type']->equals(CrudType::password()) and
            !$this->{$ruleName} or (is_string($this->{$ruleName}) and !\strlen($this->{$ruleName}))
        ) {
            $this->offsetUnset($ruleName);
        }
        // handle boolean
        if ($prop['type']->equals(CrudType::boolean())) {
            $this->merge([$ruleName => $this->{$ruleName} ? 1 : 0]);
        }
        // handle image
        if ($prop['type']->equals(CrudType::image())) {
            $tableName = Str::singular((new $model())->getTable());
            if ($this->{$tableName}) {
                $i = array_search('required', $out);
                unset($propsRules[$ruleName][$i]);
            }
        }
        // handle belongsToMany
        if ($prop['type']->equals(CrudType::belongsToMany())) {
            $propsRules["$ruleName.*"][0] = 'exists:' .
                (new $prop['belongsToMany']())->getTable() . ',id';
            if (!isset($this->{$ruleName})) {
                $this->merge([$ruleName => []]);
            }
        }
    }

    /**
     * Returns the first parameter that is a crud model
     *
     * @return string \Illuminate\Database\Eloquent\Model class name
     * @throws \App\Exceptions\CrudException if the request does not have a crud model parameter
     */
    protected function getCurrentModel(): string
    {
        if (
            !is_object($this->route()->controller) or
            !in_array(CrudController::class, class_uses_recursive($this->route()->controller))
        ) {
            throw new CrudException(sprintf(
                'Request `%s` needs to be bound on route that use a CrudController trait',
                get_class($this)
            ));
        }
        return $this->route()->controller->getModelClass();
    }
}
