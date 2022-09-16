# Changelog

All notable changes to `CrudPolicies` will be documented in this file.

# version 0.4.8
- Change moveUploadedFile as protected to allow overload

# version 0.4.7
- Fix show nullable dates and datetimes

# version 0.4.6
- Fix transFb fallback always return string
- Check belongsToMany cascade restrict on delete

# version 0.4.5
- Fix layout without table
- Fix printfield belongsTo

# version 0.4.4
- Make package CSP more compliant (extract vue styles)
- CSP style inline is still needed for CKEditor to work
- Added sources Maps

# version 0.4.3
- Fixed CSP using CkEditor and SweetAlert2 (no require of unsafe inline)

# version 0.4.2
- Make package CSP compliant
# version 0.4.1
- Fixed vue compile

# version 0.4.0
- Use vue runtime instead of vue runtime-compiler
- updated npm deps + audit fix
- Update CKEditor JS
- Add Manual CSRK Token to axios boot is necessary
- Fixed UploadFileRequest with ajax

# version 0.3.7
- Hot fixed hasMany view component
- Added translate in CrudController message

# version 0.3.6
- Add date and datetime input types
- Fix select inputs bootstrap class
- Fix check belongsTo prop on model
- Fix checkbox readonly is marked as disabled
- Fix saveRelations check if hasMany update requested before throwing exception

# version 0.3.5
- Fixed check if parent constructor exists in CrudController trait
- Fixed npm dependencies update + audit fix
- Changed phpcs rules + code comment
- Added hasMany relation with possibility to save only when nullable.

# version 0.3.4
- Added missing assets from previous release
- Fix potential getPrefix() from laravel adding forward slash

# version 0.3.3
 - Added option to show delete action on index or hide
 - Added option to display or hide flash messages
 - Added missing ckeditor source build 
 - Changed update npm deps
 - Changed update ckeditor build
 - Changed conditionnal loading of CKEDITOR to prevent errors using multiple builds
 - Fix color pad

# version 0.3.2
- Added CrudController callbacks showModel, createModel, editModel

# version 0.3.1
- Fixed CrudController trait : call parent::_construct
# version 0.3.0
- Changed !! IMPORTANT Drop support for laravel 7
- Added missing doctrine/dbal dependency

# version 0.2.1
- Changed Now displays color field type with its css color
- Fixed Properly load config files
- Added mews/purifier dependecy (htmlpurifier) to display text fields

# version 0.2.0
- Reworked text input with CKeditor to allow image upload
- Fixed Internal packages route could not be secured
  So you can add middlewares to secure your route, for example if
  you wish to use default session authenticate you may add the following middlewares
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Session\Middleware\StartSession::class,
    'auth:web'


# version 0.1.22
- Fixed "Propriété" string
- Fixed sort urls + reset button

# version 0.1.21
- Fixed column sorter reset button

# version 0.1.20
- Added model.show route now returns a resource when and ajax call is made

# version 0.1.19
- Fixed duplicated rules in validate CrudRequest
- Fixed handle nullable in CrudRequest

# version 0.1.18
- Fixed column sorter not using url generation parameters when forced

# version 0.1.16
- Added phpcs slevomat/coding-standard
- Changed bump squizlabs/php_codesniffer to 3.6.0
- Fixed Json editor error
- Fixed phpcs errors

# Version 0.1.15
- Added place holder on search index
- Changed simplify sorter url build

# Version 0.1.14
- Fixed print field belongsTo Filedname

# Version 0.1.13
- Changed kwaadpepper/enum to 2.0.0

# Version 0.1.12
-  Fixed belongsTo not saving

# Version 0.1.11
- Fixed vuejs specific
- Fixed css class formfield input type image
- Fixed css class formfield input type image

## Version 0.1.9
- Added type file and uri
- Fixed redirections using autorisations
- Changed breadcrumb rework with inline-flex
- Changed actionbar refactor

## Version 0.1.8
- Fixed breadcrumb nested resources
- Fixed sorter reset btn + destroy alert

## Version 0.1.7
- Added some support for nested resources
- Added delete and deleted model hook on controller trait
- Fixed belongsTo and belongstoMany validation
- Fixed ckeditor issue (document loaded check)
- Fixed invalid html
- Fixed fill method crash when belongsTo and belongsToMany are in fillable array

## Version 0.1.2
- Fix remove route home from default layout
- Fix issue in crud request
- Added input type color
- Added missing translations in enumerations

## Version 0.1.6
- Fixed check if relation has member before delete, otherwise allow delete

## Version 0.1.5
- Fixed crud.view delete button

## Version 0.1.4
- Added modes to json fields
- Added default values option

## Version 0.1.3
- Added Model names can be translated under models.classes.ModelClass with its plural version
- Added function helper transFb (translate with fallback)
- Added unsigned int type
- Added unsigned float type
- Added json editor
- Fixed some html on crud.index view

## Version 0.1.1
- Fix FR translation

## Version 0.1.0
- Moved code into traits
- Moved views + assets in packages
- Service provider can publish any content
- Routes exists to serve ajax indexes, to serve static content
- translations are sets
- configuration is now possible
- It is possible to overload the layout only
## Version 0.0.1
First version, tried to separate code from portail S.



