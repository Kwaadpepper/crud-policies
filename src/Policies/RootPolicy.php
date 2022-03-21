<?php

namespace Kwaadpepper\CrudPolicies\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Kwaadpepper\CrudPolicies\Policies\Rules\StaticRules;

class RootPolicy
{
    /** @var \Kwaadpepper\CrudPolicies\Policies\AppPolicies  The current application policy class name */
    protected $policy = null;

    /** @var array The application policies */
    protected $policies = null;

    /**
     * RootPolicy
     */
    public function __construct()
    {
        $cls          = \explode('\\', \get_called_class());
        $this->policy = \strtolower(\str_replace('Policy', '', \end($cls)));
    }

    /**
     * Policy logic to apply.
     *
     * @param string     $method
     * @param string     $policy
     * @param Model      $user
     * @param Model|null $model
     * @return Response
     */
    protected function actionPolicy(string $method, string $policy, Model $user, Model $model = null): Response
    {
        $this->setPolicies($user, $model);
        $ctx = [$policy, $user, $model];

        if (!$this->hasPolicy($policy)) {
            $m = __('Unhandled policy ":policy"', ['policy' => $policy]);
            Log::critical($m, $ctx);
            return $this->deny($m);
        }

        if (!is_array($this->policies[$policy])) {
            $m = __('Policy ":policy" has to be array', ['policy' => $policy]);
            Log::critical($m, $ctx);
            return $this->deny($m);
        }

        // * Policy method exists
        if (!array_key_exists($method, $this->policies[$policy])) {
            $m = __('Unhandled policy ":policy" for action ":action"', ['policy' => $policy, 'action' => $method]);
            Log::critical($m, $ctx);
            return $this->deny($m);
        }

        // * Call policy method
        if (StaticRules::handle($this->policies[$policy][$method], $user, $model)) {
            return $this->allow();
        } else {
            $m = __('Policy ":policy" for action ":action" denied :onmodel', [
                'policy' => $policy,
                'action' => $method,
                'onmodel' => $model ? get_class($model) : ''
            ]);
            if (config('app.policyDebug')) {
                Log::debug(sprintf("%s user => %s model => %s", $m, $user, $model), $ctx);
            }
            return $this->deny($m);
        }

        $m = __('Unhandled policy case on resource ":method"', ['method' => $method]);
        Log::critical($m, $ctx);
        return $this->deny();
    }

    /**
     * Do the magic calling Root policy from any policy
     *
     * @param mixed $method
     * @param mixed $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this, 'actionPolicy'], array_merge([$method, $this->policy], $args));
    }

    /**
     * Check if a policy exists
     *
     * @param string $policy
     * @return boolean
     */
    private function hasPolicy(string $policy): bool
    {
        return array_key_exists($policy, $this->policies);
    }

    /**
     * Allow action for the user
     *
     * @param string|null $message
     * @param mixed|null  $code
     * @return \Illuminate\Auth\Access\Response
     */
    protected function allow(?string $message = null, $code = null)
    {
        return Response::allow($message, $code);
    }

    /**
     * Deny action for the user
     *
     * @param string|null $message
     * @param mixed|null  $code
     * @return \Illuminate\Auth\Access\Response
     */
    protected function deny(?string $message = null, $code = null)
    {
        if (!$message) {
            $message = __('This action is not allowed to you for this resource.');
        }
        return Response::deny($message, $code);
    }
}
