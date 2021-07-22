# Changelog

All notable changes to `CrudPolicies` will be documented in this file.

## Version 0.0.1
First version, tried to separate code from portail S.

## Version 0.1.0
- Moved code into traits
- Moved views + assets in packages
- Service provider can publish any content
- Routes exists to serve ajax indexes, to serve static content
- translations are sets
- configuration is now possible
- It is possible to overload the layout only

## Version 0.1.1
- Fix FR translation

## Version 0.1.2
- Fix remove route home from default layout
- Fix issue in crud request
- Added input type color
- Added missing translations in enumerations

## Version 0.1.3
- Added Model names can be translated under models.classes.ModelClass with its plural version
- Added function helper transFb (translate with fallback)
- Added unsigned int type
- Added unsigned float type
- Added json editor
- Fixed some html on crud.index view

## Version 0.1.4
- Added modes to json fields
- Added default values option

## Version 0.1.5
- Fixed crud.view delete button

## Version 0.1.6
- Fixed check if relation has member before delete, otherwise allow delete
