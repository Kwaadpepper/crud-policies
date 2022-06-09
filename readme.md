# Crud + Policies for scaffolding work purposes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

Resizes an image on the fly and returns the new link

## Installation

   composer require kwaadpepper/crud-policies


## CSP
If you wish to make this package CSP compliant you shall
share to all views a `$nonce` variable.

## Usage

1. <a href="#create-a-model-iscrudmodel">Create a model</a> that will use the ```Kwaadpepper\CrudPolicies\IsCrudModel``` trait
2. <a href="#create-a-controller-crudcontroller">Create a controller</a> that will use the ```Kwaadpepper\CrudPolicies\CrudController``` trait
3. <a href="#create-a-policy-rootpolicy">Create a policy</a> that will extends the ```Kwaadpepper\CrudPolicies\Policies\RootPolicy``` class

### Create a Model (IsCrudModel)

 * Use ~~```php artisan make:crudModel  ModelName```~~
 * Do it by hand

If You choose to do it by hand take the file [examples/CrudModel.php](examples/CrudModel.php) as an example.

Some infos :
- The model has to use ```IsCrudModel``` trait
- rules for models fields has to be **set in the constructor** as the provided example (*$editableProperties* prop)
- Each **CrudType** enum is handled in a specific way more doc will be to come, or are welcome if you are willing to write it
- **Requests validation** are constructed from the model for *create* and *update* actions
- Your model has to set **```protected $crudLabelColumn = 'string column name';```**
- Your model has to set **```protected $crudValueColumn = 'unique constrained column name';```**

**! The two last points are specially needed for relations !**

---

### Create a Controller (CrudController)

 * Use ~~```php artisan make:crudController  ControllerName```~~
 * Do it by hand

If You choose to do it by hand take the file [examples/CrudController.php](examples/CrudController.php) as an example.

Some hooks are available in the controller, you can also overload methods to handle your own way the think

You can of course write your own methods to handle the rest of your application.

More documentation is yet to come, dont hesite to check in code directly

---

### Create a Policy (RootPolicy)

 * Use ~~```php artisan make:policy ModelName```~~
 * Do it by hand

If You choose to do it by hand take the file [examples/CrudPolicy.php](examples/CrudPolicy.php) as an example.

Note that policies rules make method returns a boolean value.
You can find an example of rules in [examples/UserRoleRules.php](examples/UserRoleRules.php)

---
## **NOTE: Artisan make commands yet has to be developped**
## Notes:
- For crud type images, you must do ```php artisan storage:link```

---

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

SOON available

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email github@jeremydev.ovh instead of using the issue tracker.

## Credits

- [Jérémy Munsch](https://jeremydev.ovh)

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kwaadpepper/crud-policies?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kwaadpepper/crud-policies?style=flat-square
[ico-travis]: https://img.shields.io/travis/kwaadpepper/crud-policies/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kwaadpepper/crud-policies
[link-downloads]: https://packagist.org/packages/kwaadpepper/crud-policies
[link-travis]: https://travis-ci.org/kwaadpepper/crud-policies
[link-author]: https://github.com/kwaadpepper
