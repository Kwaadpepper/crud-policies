<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kwaadpepper\CrudPolicies\Enums\CrudAction;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Traits\IsCrudModel;

class CrudModel extends Authenticatable
{
    use HasFactory;
    use IsCrudModel;

    protected $crudLabelColumn = 'email';

    protected $crudValueColumn = 'id';

    protected $fillable = [
        'registration',
        'firstname',
        'lastname',
        'email',
        'site_id'
    ];

    public function __construct($attributes = [])
    {
        self::$editableProperties = [
            'registration' => [
                'label' => trans('Registration number'),
                'type' => CrudType::string(),
                'readonly' => true,
                'validate' => [
                    'digits_between:7,8',
                    // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
                    function ($attribute, $value, $fail) {
                        $q = CrudModel::query();
                        if ($this->id) {
                            $q = $q->where('id', '!=', $this->id);
                        }
                        if ($q->where('registration', $value)->exists()) {
                            $fail(__(':attribute is already used', [
                                'attribute' => $attribute
                            ]));
                        }
                    }
                ],
                'actions' => [
                    CrudAction::viewAny(),
                    CrudAction::view(),
                    CrudAction::create(),
                    CrudAction::update()
                ]
            ],
            'firstname' => [
                'label' => trans('First name'),
                'type' => CrudType::string(),
                'validate' => [
                    'string',
                    'max:60'
                ],
                'actions' => [
                    CrudAction::view(),
                    CrudAction::create(),
                    CrudAction::update(),
                ]
            ],
            'lastname' => [
                'label' => trans('Last name'),
                'type' => CrudType::string(),
                'nullable' => false,
                'validate' => [
                    'string',
                    'max:60'
                ],
                'actions' => [
                    CrudAction::viewAny(),
                    CrudAction::view(),
                    CrudAction::create(),
                    CrudAction::update(),
                ]
            ],
            'email' => [
                'label' => trans('Email'),
                'type' => CrudType::email(),
                'nullable' => false,
                'validate' => [
                    'max:150',
                    // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
                    function ($attribute, $value, $fail) {
                        $q = CrudModel::query();
                        if ($this->id) {
                            $q = $q->where('id', '!=', $this->id);
                        }
                        if ($q->where('email', $value)->exists()) {
                            $fail(__(':attribute is already used', [
                                'attribute' => $attribute
                            ]));
                        }
                    }
                ],
                'actions' => [
                    CrudAction::viewAny(),
                    CrudAction::view(),
                    CrudAction::create(),
                    CrudAction::update(),
                ]
            ],
            'password' => [
                'label' => trans('Password'),
                'placeholder' => '#M1s3Cure.P4ss0rd!',
                'type' => CrudType::password(),
                'nullable' => true,
                'validate' => [
                    'max:150'
                ],
                'actions' => [
                    CrudAction::create(),
                    CrudAction::delete()
                ]
            ],
            'site_id' => [
                'label' => trans('Site'),
                'type' => CrudType::belongsTo(),
                'belongsTo' => Site::class,
                'actions' => [
                    CrudAction::create(),
                    CrudAction::update()
                ]
            ],
            'blocked' => [
                'label' => trans('Blocked'),
                'type' => CrudType::boolean(),
                'actions' => [
                    CrudAction::viewAny(),
                    CrudAction::view(),
                    CrudAction::update(),
                ],
                'getAttribute' => function (CrudModel $model) {
                    return $model->getAttribute('password_attempts') >= 3;
                },
                'setAttribute' => function (CrudModel $model, $value) {
                    $model->setAttribute('password_attempts', $value ? 3 : 0);
                    return $model->getAttribute('password_attempts');
                },
            ]
        ];
        parent::__construct($attributes);
    }

    /**
     * Has one Site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
