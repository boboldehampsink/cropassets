Crop Assets plugin for Craft CMS [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nerds-and-company/cropassets/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/boboldehampsink/cropassets/?branch=master) [![Latest Stable Version](https://poser.pugx.org/nerds-and-company/cropassets/v/stable)](https://packagist.org/packages/nerds-and-company/cropassets) [![Total Downloads](https://poser.pugx.org/nerds-and-company/cropassets/downloads)](https://packagist.org/packages/nerds-and-company/cropassets) [![Latest Unstable Version](https://poser.pugx.org/nerds-and-company/cropassets/v/unstable)](https://packagist.org/packages/nerds-and-company/cropassets) [![License](https://poser.pugx.org/nerds-and-company/cropassets/license)](https://packagist.org/packages/nerds-and-company/cropassets)
=================

Plugin/FieldType that allows you to crop an asset and show it in the front-end.

__Important__  
The plugin's folder should be named "cropassets"  

Install
=================

This plugin depends on [cropper.js](https://github.com/fengyuanchen/cropperjs)
Composer will install this package.
When not user composer, either run npm install in the plugin root directory or extract cropperjs into the PLUGIN_ROOT/node_modules/cropperjs folder.

Usage
=================
This plugin provides a Crop Assets FieldType that works like the Asset FieldType.  
You can upload/select an asset and a crop modal will appear.
After cropping you can show the cropped asset in the front-end.

Changelog
=================
###2.0.0###
 - Rebuild to make use of client-side cropping with [cropperjs](https://github.com/fengyuanchen/cropperjs)
 - This is mainly done to prevent memory issues on the server.
 - The cropped asset is stored in a configured asset source
 - Renamed vendor to nerds-and-company

###1.0.2###
 - Verify that the image is present to prevent errors

###1.0.1###
 - Fixed case sensitive path to js resource

###1.0.0###
 - Initial release
