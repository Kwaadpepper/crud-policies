<?php

namespace Kwaadpepper\CrudPolicies\Policies\Rules;

use Illuminate\Database\Eloquent\Model;

/**
 * Generic Policiy Static Rules
 * ! Rules has to return boolean value
 */
abstract class StaticRules
{
    /**
     * Make a rule closure
     *
     * @param string $rulename
     * @param mixed  ...$args
     * @return Closure
     * @throws \ErrorException If the rule is unknown.
     */
    public static function make(string $rulename, ...$args)
    {
        if (!method_exists(static::class, $rulename)) {
            throw new \ErrorException(sprintf('Unknown rule %s in Policies StaticRules', $rulename));
        }
        return function () use ($rulename, $args) {
            return call_user_func_array([static::class, $rulename], $args);
        };
    }

    /**
     * Provides AND function for policies rules usage
     *
     * @param array $ruleClosures
     * @return callable
     */
    public static function and(array $ruleClosures): callable
    {
        return function (Model $user, Model $model = null) use ($ruleClosures) {
            $nbRules = count($ruleClosures);
            $out     = true;
            $i       = 0;
            do {
                if (!($out = (self::handle($ruleClosures[$i++], $user, $model) && $out))) {
                    break;
                }
            } while ($i < $nbRules);
            return $out;
        };
    }

    /**
     * Provides OR function for policies rules usage
     *
     * @param array $ruleClosures
     * @return callable
     */
    public static function or(array $ruleClosures): callable
    {
        return function (Model $user, Model $model = null) use ($ruleClosures) {
            $nbRules = count($ruleClosures);
            $out     = false;
            $i       = 0;
            do {
                if ($out = (self::handle($ruleClosures[$i++], $user, $model) || $out)) {
                    break;
                }
            } while ($i < $nbRules);
            return $out;
        };
    }

    /**
     * Handles one or multiple rules for a specific policy
     *
     * @param array|callable $rule
     * @param Model          $user
     * @param Model          $model
     * @return boolean
     * @throws \ErrorException If the rule is not callable.
     */
    public static function handle($rule, Model $user, Model $model = null): bool
    {
        if (is_array($rule)) {
            $nbRule = count($rule);
            $out    = false;
            $i      = 0;
            while ($i < $nbRule) {
                if ($out = self::handle($rule[$i++], $user, $model)) {
                    break;
                }
            }
            return $out;
        }
        if (!is_callable($rule)) {
            throw new \ErrorException('Rule has to ba callable.');
        }
        return (bool)$rule($user, $model);
    }

    /**
     * Always allow.
     *
     * @return true
     */
    protected static function anyone()
    {
        return true;
    }
}
