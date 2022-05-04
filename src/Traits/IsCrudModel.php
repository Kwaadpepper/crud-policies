<?php

namespace Kwaadpepper\CrudPolicies\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Exceptions\CrudException;
use Kwaadpepper\CrudPolicies\Http\Requests\CrudRequest;
use Kwaadpepper\Enum\BaseEnum;
use Illuminate\Support\Str;

/**
 * Make a model become a CRUD model
 */
trait IsCrudModel
{
    /**
     * Contain all crud configuration for this model.
     *
     * @var array
     */
    protected static $editableProperties = [];

    /**
     * The base schema of available properties.
     *
     * @var array
     */
    private static $schemaCrud = [
        'label' => null,
        'placeholder' => '',
        'default' => null,
        'type' => null,
        'enum' => null,
        'nullable' => false,
        'mode' => 'code',
        'modes' => ['form', 'code', 'text'],
        'readonly' => false,
        'disabled' => false,
        'required' => false,
        'accept' => '',
        'validate' => [],
        'rules' => [],
        'actions' => [],
        'hasMany' => null,
        'belongsTo' => null,
        'belongsToMany' => null,
        'getAttribute' => null,
        'setAttribute' => null,
    ];

    /**
     * OVERLOAD MODEL FUNCTION getter
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        if (
            isset(static::$editableProperties[$key]['getAttribute']) and
            is_callable(static::$editableProperties[$key]['getAttribute'])
        ) {
            return static::$editableProperties[$key]['getAttribute']($this);
        }
        return parent::__get($key);
    }

    /**
     * OVERLOAD MODEL FUNCTION setter
     *
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        if (
            isset($this::$editableProperties[$key]['setAttribute']) and
            is_callable($this::$editableProperties[$key]['setAttribute'])
        ) {
            return $this::$editableProperties[$key]['setAttribute']($this, $value);
        }
        return parent::__set($key, $value);
    }

    /**
     * OVERLOAD MODEL FUNCTION Laravel fill method
     *
     * @param array $attributes
     * @return void
     */
    public function fill(array $attributes)
    {
        $cpAttributes = [];
        foreach ($attributes as $k => $attribute) {
            if (
                isset(static::$editableProperties[$k]['type']) and
                (static::$editableProperties[$k]['type']->equals(CrudType::hasMany()) or
                static::$editableProperties[$k]['type']->equals(CrudType::belongsToMany())
                )
            ) {
                continue;
            }
            $cpAttributes[$k] = $attribute;
        }
        parent::fill($cpAttributes);
        foreach ($cpAttributes as $k => $attribute) {
            if (
                isset(static::$editableProperties[$k]['setAttribute']) and
                is_callable(static::$editableProperties[$k]['setAttribute'])
            ) {
                $this::$editableProperties[$k]['setAttribute']($this, $attribute);
            }
            if (\is_object($this->{$k}) and \get_class($this->{$k}) === UploadedFile::class) {
                $this->moveUploadedFile($k, $this->{$k});
            }
        }
    }

    /**
     * Save model relations
     *
     * @param CrudRequest $request
     * @return void
     * @throws CrudException If has many key is not nullable while saving or could not be resolved.
     */
    public function saveRelations(CrudRequest $request)
    {
        $validated = $request->validated();
        $props     = self::getEditableProperties();
        foreach ($props as $k => $prop) {
            if ($prop['belongsToMany']) {
                $this->{$k}()->sync(is_array($validated[$k]) ? $validated[$k] : []);
            }
            if ($prop['hasMany'] and !$prop['readonly']) {
                /** @var \Illuminate\Database\Eloquent\Model $modelClass */
                $modelClass = (new $props[$k]['hasMany']());
                $tableName  = $modelClass->getTable();
                $foreignKey = sprintf('%s_id', Str::singular($this->getTable()));

                /** @var object|null */
                $fieldDesc = collect(DB::select('show columns from ' . $tableName))
                    ->filter(function (object $columnDesc) use ($foreignKey) {
                        return $columnDesc->Field === $foreignKey;
                    })->first();
                if (!$fieldDesc) {
                    throw new CrudException("$foreignKey not found in $tableName .");
                }
                /** @var boolean $isNullable */
                $isNullable = Str::lower($fieldDesc->Null) === 'yes';
                if ($isNullable) {
                    // * If column is nullable set null (not normal behavior of hasMany)
                    $this->{$k}->map(function (Model $model) {
                        $model->{sprintf('%s_id', Str::singular($this->getTable()))} = null;
                        $model->saveOrFail();
                    });
                    /** @var \Illuminate\Database\Eloquent\Collection $associateModels */
                    $associateModels = $props[$k]['hasMany']::whereIn('id', $validated)->get();
                    // * Associate models
                    $associateModels->map(function (Model $model) {
                        $model->{sprintf('%s_id', Str::singular($this->getTable()))} = $this->id;
                        $model->saveOrFail();
                    });
                } else {
                    // * Normal behavior
                    // ! Cannot do this as this would require to create or delete on the fly associated model.
                    throw new CrudException(
                        'It is not possible to change associated hasMany relation with a non nullable foreign key.'
                    );
                }
            } //end if
        }//end foreach
    }

    /**
     * Has this model Has many relations with delete restrict ?
     *
     * @return boolean|string
     */
    public function gotHasManyRelationWithRestrictOnDelete()
    {
        $relations = [];
        $sm        = DB::getDoctrineSchemaManager();
        foreach (self::getRelationships() as $relation => $type) {
            if ($type[0] === 'HasMany') {
                $table = (new $type[1]())->getTable();
                /** @var \Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey */
                foreach ($sm->listTableForeignKeys($table) as $foreignKey) {
                    // /vendor/doctrine/dbal/src/Schema/ForeignKeyConstraint.php onEvent()
                    if ($foreignKey->onDelete() === 'CASCADE') {
                        // Allow only CASCADE action, otherwise it would break the app .
                        continue;
                    }
                    $relations[] = [
                        'relation' => $relation,
                        'className' => class_basename($type[1])
                    ];
                }
            }
        }
        return count($relations) ? $relations : false;
    }

    /**
     * Get the crud label
     *
     * @return mixed
     */
    public function getCrudLabelAttribute()
    {
        return $this->{$this->crudLabelColumn};
    }

    /**
     * Get the crud value
     *
     * @return mixed
     * @throws CrudException If belongsTo property is not set.
     */
    public function getCrudValueAttribute()
    {
        if (is_null($this->crudValueColumn)) {
            throw new CrudException(sprintf(
                '[\'belongsTo\'] model `%s` must have `crudValueColumn` property',
                get_class($this),
                CrudAction::class
            ));
        }
        return $this->{$this->crudValueColumn};
    }

    /**
     * Get the crud label column
     *
     * @return mixed
     * @throws CrudException If belongsTo property is not set.
     */
    public function getCrudLabelColumnAttribute()
    {
        if (is_null($this->crudLabelColumn)) {
            throw new CrudException(sprintf(
                '[\'belongsTo\'] model `%s` must have `crudLabelColumn` property',
                get_class($this),
                CrudAction::class
            ));
        }
        return $this->crudLabelColumn;
    }

    /**
     * Get the crud value column
     *
     * @return mixed
     */
    public function getCrudValueColumnAttribute()
    {
        return $this->crudValueColumn;
    }

    /**
     * Get the model validation rules
     *
     * @return array
     * @phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    public static function getRules(): array
    {
        return collect(static::$editableProperties)->mapWithKeys(function ($item, $key) {
            $item         = \array_merge(self::$schemaCrud, $item);
            $defaultRules = [];
            if (!$item['nullable'] === true) {
                $defaultRules[] = 'required';
            } elseif ($item['required'] === true) {
                $defaultRules[] = 'required';
            } elseif ($item['readonly'] === true) {
                $defaultRules[] = 'prohibited';
            }
            switch ($item['type']) {
                case CrudType::boolean():
                    $defaultRules[] = 'boolean';
                    break;
                case CrudType::file():
                case CrudType::image():
                    $defaultRules[] = 'file';
                    break;
                case CrudType::email():
                    $defaultRules[] = 'email:rfc,dns';
                    break;
                case CrudType::unsignedfloat():
                    $defaultRules[] = 'min:0';
                    break;
                case CrudType::unsignedint():
                case CrudType::belongsTo():
                    $defaultRules[] = 'integer';
                    $defaultRules[] = 'min:0';
                    break;
                case CrudType::int():
                    $defaultRules[] = 'integer';
                    break;
                case CrudType::belongsToMany():
                case CrudType::hasMany():
                    $defaultRules[] = 'array';
                    break;
                case CrudType::password():
                    // phpcs:ignore Generic.Files.LineLength.TooLong
                    $defaultRules[] = "regex:/^(?=.*[A-Z])(?=.*[²&~\"#'\{\(\[\-|`_\\^@\)\°=+\}¨^\$£¤%?,;.:\/!§*<>])(?=.*[0-9].*)(?=.*[a-z]).{8,}$/";
                    break;
                case CrudType::text():
                case CrudType::string():
                default:
                    $defaultRules[] = 'string';
                    break;
            }//end switch
            return [$key => array_merge($defaultRules, $item['validate'] ?? self::$schemaCrud['validate'])];
        })->all();
    }

    /**
     * Get the model Name
     *
     * @return string
     */
    public function getModelName(): string
    {
        return class_basename(get_class($this));
    }

    /**
     * moveUploadedFile helper
     *
     * @param string       $propName
     * @param UploadedFile $file
     * @return void
     */
    private function moveUploadedFile(string $propName, UploadedFile &$file): void
    {
        /** @var \Illuminate\Database\Eloquent\Model */
        $self              = $this;
        $this->{$propName} = $file->storePubliclyAs(
            $self->getTable(),
            $file->hashName(),
            ['disk' => 'public']
        );
    }

    /**
     * Get the Editable Properties
     *
     * @return array
     */
    public static function getEditableProperties(): array
    {
        $rules = self::getRules();
        static::assertEditablePropertiesIsCorrect();
        return \collect(static::$editableProperties)->mapWithKeys(function ($prop, $key) use ($rules) {
            if (isset($rules[$key])) {
                if (is_array($rules[$key])) {
                    $prop['rules'] = \array_merge($rules[$key], $prop['rules']);
                }
                if (is_string($rules[$key])) {
                    $prop['rules'] = $rules[$key];
                }
            }
            return [$key => $prop];
        })->all();
    }

    /**
     * Get eloquent relationships
     *
     * @url https://laracasts.com/discuss/channels/eloquent/get-all-model-relationships
     *
     * @return array
     */
    private static function getRelationships(): array
    {
        $instance = new static();

        // Get public methods declared without parameters and non inherited.
        $class      = get_class($instance);
        $allMethods = (new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods    = array_filter(
            $allMethods,
            function ($method) use ($class) {
                return $method->class === $class
                    // Relationships have no parameters.
                    && !$method->getParameters()
                    // Prevent infinite recursion.
                    && $method->getName() !== 'getRelationships'
                    // Prevent infinite recursion.
                    && $method->getName() !== 'gotHasManyRelationWithRestrictOnDelete';
            }
        );

        DB::beginTransaction();

        $relations = [];
        foreach ($methods as $method) {
            try {
                $methodName   = $method->getName();
                $methodReturn = $instance->$methodName();
                if (!$methodReturn instanceof Relation) {
                    continue;
                }
            } catch (\Throwable $th) {
                continue;
            }

            $type                   = (new \ReflectionClass($methodReturn))->getShortName();
            $model                  = get_class($methodReturn->getRelated());
            $relations[$methodName] = [$type, $model];
        }

        DB::rollBack();

        return $relations;
    }

    /** --- ASSERTIONS --- */

    /**
     * Assert  self::$editableProperties is in a correct format
     *
     * 'string' => CrudType::value()
     *
     * @return void
     * @throws CrudException Is anything is wrong in configuration.
     */
    private static function assertEditablePropertiesIsCorrect(): void
    {
        if (empty(static::$editableProperties)) {
            throw new CrudException('$editableProperties must be defined in ' . __CLASS__);
        }
        foreach (static::$editableProperties as $name => &$prop) {
            if (!is_string($name)) {
                throw new CrudException(sprintf(
                    'editableProperties array value must be an array given: %s',
                    print_r($prop, true) // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Found
                ));
            }
            if (!is_array($prop)) {
                throw new CrudException(sprintf(
                    'editableProperties array value must be an array given: %s',
                    print_r($prop, true) // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Found
                ));
            }
            $prop = array_merge_recursive_distinct(self::$schemaCrud, $prop);

            // * Auto label
            $prop['label'] = $prop['label'] ?? ucfirst($name);
            self::assertLabelIscorrect($prop);
            self::assertPlaceholderIscorrect($prop);
            self::assertTypeIscorrect($prop);
            switch ($prop['type']) {
                    // * Email auto rule
                case CrudType::email():
                    $prop['validate'] = is_array($prop['validate']) ? $prop['validate'] : [];
                    if (!array_search('email:rfc,dns', $prop['validate'])) {
                        $prop['validate'][] = 'email:rfc,dns';
                    }
                    break;
                case CrudType::json():
                    $prop['validate'] = is_array($prop['validate']) ? $prop['validate'] : [];
                    if (!array_search('json', $prop['validate'])) {
                        $prop['validate'][] = 'json';
                    }
                    break;
            }
            self::assertAcceptIscorrect($prop);
            self::assertNullableIscorrect($prop);
            self::assertReadonlyIscorrect($prop);
            self::assertDisabledIscorrect($prop);
            self::assertValidateIscorrect($prop);
            self::assertRulesArecorrect($prop);
            self::assertActionsArecorrect($prop);
            self::assertBelongsToIsCorrect($prop);
            self::assertEnumIscorrect($prop);
            self::assertGetAttributeIscorrect($prop);
            self::assertSetAttributeIscorrect($prop);
            self::assertBelongsToManyIsCorrect($prop);
            self::assertHasManyIsCorrect($prop);
        }//end foreach
    }

    /**
     * Assert accept is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If accept is not correctly set.
     */
    private static function assertAcceptIscorrect(array $prop): void
    {
        if (!isset($prop['accept']) or !is_string($prop['accept'])) {
            throw new CrudException('$editableProperties[\'accept\'] array value must be a string');
        }
    }

    /**
     * Assert label is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If label is not correctly set.
     */
    private static function assertLabelIscorrect(array $prop): void
    {
        if (!isset($prop['label']) or !is_string($prop['label'])) {
            throw new CrudException('$editableProperties[\'label\'] array value must be a string');
        }
    }

    /**
     * Assert placeholder is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If placeholder is not correctly set.
     */
    private static function assertPlaceholderIscorrect(array $prop): void
    {
        if (!isset($prop['placeholder']) or !is_string($prop['placeholder'])) {
            throw new CrudException('$editableProperties[\'placeholder\'] array value must be a string');
        }
    }

    /**
     * Assert type is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If type is not correctly set.
     */
    private static function assertTypeIscorrect(array $prop): void
    {
        if (!isset($prop['type']) or !is_object($prop['type']) or get_class($prop['type']) !== CrudType::class) {
            throw new CrudException('$editableProperties[\'type\'] value must be a CrudType enum');
        }
    }

    /**
     * Assert nullable is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If nullable is not correctly set.
     */
    private static function assertNullableIscorrect(array $prop): void
    {
        if (!isset($prop['nullable']) or !is_bool($prop['nullable'])) {
            throw new CrudException('$editableProperties[\'nullable\'] array value must be a boolean');
        }
    }

    /**
     * Assert readonly is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If readonly is not correctly set.
     */
    private static function assertReadonlyIscorrect(array $prop): void
    {
        if (
            !isset($prop['readonly']) or
            (!is_bool($prop['readonly']) and !\is_callable($prop['readonly']))
        ) {
            // phpcs:ignore Generic.Files.LineLength.TooLong
            throw new CrudException('$editableProperties[\'readonly\'] array value must be a boolean or a function returnning a boolean');
        }
    }

    /**
     * Assert disabled is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If disabled is not correctly set.
     */
    private static function assertDisabledIscorrect(array $prop): void
    {
        if (!isset($prop['disabled']) or !is_bool($prop['disabled'])) {
            throw new CrudException('$editableProperties[\'disabled\'] array value must be a boolean');
        }
    }

    /**
     * Assert required is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If required is not correctly set.
     */
    private static function assertRequiredIscorrect(array $prop): void
    {
        if (!isset($prop['required']) or !is_bool($prop['required'])) {
            throw new CrudException('$editableProperties[\'required\'] array value must be a boolean');
        }
    }

    /**
     * Assert validate is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If validate is not correctly set.
     */
    private static function assertValidateIscorrect(array $prop): void
    {
        if (!isset($prop['validate']) or !\is_array($prop['validate'])) {
            throw new CrudException('$editableProperties[\'validate\'] array value must be an array');
        }
    }

    /**
     * Assert get attribute (laravel getter) is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If getAttribute is not correctly set.
     */
    private static function assertGetAttributeIscorrect(array $prop): void
    {
        if (!is_null($prop['getAttribute']) and !is_callable($prop['getAttribute'])) {
            throw new CrudException('$editableProperties[\'getAttribute\'] array value must be callable');
        }
    }

    /**
     * Assert set attribute (laravel setter) is correctly set.
     *
     * @param array $prop
     * @return void
     * @throws CrudException If setAttribute is not correctly set.
     */
    private static function assertSetAttributeIscorrect(array $prop): void
    {
        if (!is_null($prop['setAttribute']) and !is_callable($prop['setAttribute'])) {
            throw new CrudException('$editableProperties[\'setAttribute\'] array value must be callable');
        }
    }

    /**
     * Assert rules are valid
     *
     * @param array $prop
     * @return void
     * @throws CrudException If invalid rule is detected.
     */
    private static function assertRulesArecorrect(array $prop): void
    {
        $allowedRules = [
            'min' => function ($v) {
                return \is_numeric($v);
            },
            'max' => function ($v) {
                return \is_numeric($v);
            },
            'step' => function ($v) {
                return \is_numeric($v);
            },
        ];
        if (!isset($prop['rules']) or !\is_array($prop['rules'])) {
            throw new CrudException('$editableProperties[\'rules\'] array value must be an array');
        }
        if (count(array_diff_key($prop['rules'], $allowedRules))) {
            throw new CrudException(sprintf(
                '$editableProperties[\'rules\'] array contains unallowed rule, allowed are : %s',
                implode(',', array_keys($allowedRules))
            ));
        }
        foreach ($allowedRules as $k => $f) {
            if (array_key_exists($k, $prop['rules']) and !$f($prop['rules'][$k])) {
                throw new CrudException(sprintf(
                    '$editableProperties[\'rules\'] rule `%s` does not validate with value `%s`',
                    $k,
                    $prop['rules'][$k]
                ));
            }
        }
    }

    /**
     * Assert actions are correctly set
     *
     * @param array $prop
     * @return void
     * @throws CrudException If actions are not correctly set.
     */
    private static function assertActionsArecorrect(array $prop): void
    {
        if (!isset($prop['actions']) or !\is_array($prop['actions'])) {
            throw new CrudException('$editableProperties[\'actions\'] array value must be an array');
        }
        foreach ($prop['actions'] as $action) {
            if (!is_object($action) or get_class($action) !== CrudAction::class) {
                throw new CrudException(sprintf(
                    '$editableProperties[\'actions\'] array value must contain `%s` objects',
                    CrudAction::class
                ));
            }
        }
    }

    /**
     * Assert enum type is correctly set
     *
     * @param array $prop
     * @return void
     * @throws CrudException If enum type is not correctly set.
     */
    private static function assertEnumIscorrect(array $prop): void
    {
        if (
            $prop['type']->equals(CrudType::enum()) and (is_null($prop['enum']) or
                !\is_subclass_of($prop['enum'], BaseEnum::class))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'enum\'] array value must be an enum `%s`',
                BaseEnum::class
            ));
        }
    }

    /**
     * Assert belongsTo is correctly set
     *
     * @param array $prop
     * @return void
     * @throws CrudException If belongsTo is not correctly set.
     */
    private static function assertBelongsToIsCorrect(array $prop): void
    {
        if (
            $prop['type']->equals(CrudType::belongsTo()) and
            (!isset($prop['belongsTo']) or !\is_string($prop['belongsTo']) or
            !\class_exists($prop['belongsTo']) or !is_a($prop['belongsTo'], Model::class, true) or
                !in_array(IsCrudModel::class, class_uses_recursive($prop['belongsTo'])))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'belongsTo\'] array value must be a CrudModel given: `%s`',
                $prop['belongsTo']
            ));
        }
    }

    /**
     * Assert belongsToMany is correctly set
     *
     * @param array $prop
     * @return void
     * @throws CrudException If belongsToMany is not correctly set.
     */
    private static function assertBelongsToManyIsCorrect(array $prop): void
    {
        if (
            $prop['type']->equals(CrudType::belongsToMany()) and
            (!\is_string($prop['belongsToMany']) or !\class_exists($prop['belongsToMany']) or !is_a($prop['belongsToMany'], Model::class, true) or
                !in_array(IsCrudModel::class, class_uses_recursive($prop['belongsToMany'])))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'belongsToMany\'] array value must be a CrudModel given: `%s`',
                $prop['belongsToMany']
            ));
        }
        if (
            $prop['type']->equals(CrudType::belongsToMany()) and
            (!\is_string($prop['belongsToManyLabel']))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'belongsToManyLabel\'] must be string using belongsToMany, given: `%s`',
                $prop['belongsToMany']
            ));
        }
    }

    /**
     * Assert hasMany is correctly set
     *
     * @param array $prop
     * @return void
     * @throws CrudException If hasMany is not correctly set.
     */
    private static function assertHasManyIsCorrect(array $prop): void
    {
        if (
            $prop['type']->equals(CrudType::hasMany()) and
            (!\is_string($prop['hasMany']) or
                !\class_exists($prop['hasMany']) or
                !is_a($prop['hasMany'], Model::class, true) or
                !in_array(IsCrudModel::class, class_uses_recursive($prop['hasMany'])))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'hasMany\'] array value must be a CrudModel given: `%s`',
                $prop['hasMany']
            ));
        }
        if (
            $prop['type']->equals(CrudType::hasMany()) and
            (!\is_string($prop['hasManyLabel']))
        ) {
            throw new CrudException(sprintf(
                '$editableProperties[\'hasManyLabel\'] must be string using hasMany, given: `%s`',
                $prop['hasMany']
            ));
        }
    }
}
