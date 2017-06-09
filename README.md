Schema Builder
==============
[![Packagist Version](https://img.shields.io/packagist/v/tunecino/yii2-schema-builder.svg?style=flat-square)](https://packagist.org/packages/tunecino/yii2-nested-rest)
[![Total Downloads](https://img.shields.io/packagist/dt/tunecino/yii2-schema-builder.svg?style=flat-square)](https://packagist.org/packages/tunecino/yii2-nested-rest)

GUI built on top of Gii, migration tool, and other extensions to quickly prototype and generate working apps.

![schema-builder-cover](https://user-images.githubusercontent.com/5133397/26989909-c72f10f8-4d4c-11e7-897f-0a8d06000d46.png)


How it works 
------------
It basically provides two things:

 - A user interface like a workbench tool to quickly prototype application schema (entities,attributes,relations,..)
 - A flexible list of commands to be dynamically generated based on a selected schema and its predefined settings before rendering them in a terminal. Those commands will do stuff like cleaning DB, create and execute migrations, use [Gii](https://github.com/yiisoft/yii2-gii) to generate models, CRUDs, ...

:bangbang: **IMPORTANT:**

> This extension is meant **to be used with new creations only**. If Gii UI has the decency to ask before overriding stuff, this extension won't. Its default Gii console commands are labeled by  `--interactive=0` and `--overwrite=1 ` flags. So **it will literally destroy your DATABASE plus any existing code found on its way**.


 Installation
------------

Start by [installing a new template](http://www.yiiframework.com/doc-2.0/guide-start-installation.html) and configuring DB. Then you can install this extension through [composer](http://getcomposer.org/download/) by either running

```
php composer.phar require --prefer-dist tunecino/yii2-schema-builder "*"
```

or by adding

```
"tunecino/yii2-schema-builder": "*"
```

to the require-dev section of your `composer.json` file.


Usage
-----

Once the extension is installed, add it to **both** `web.php` and `console.php` configuration files:

```php
/* preferably to add under 'dev' environment */

if (YII_ENV_DEV) {
   
    $config['modules']['schema-builder'] = [
        'class' => 'tunecino\builder\Module',
        // uncomment the following and add IP if not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        //'yiiScript' => Yii::getAlias('@root'). '/yii', // adjust path to point to your ./yii script
    ];
    
}
```

You can then access it through the following URL:
```
http://localhost/path/to/index.php?r=schema-builder
```
or `http://localhost/path/to/index.php/schema-builder` depending on your app routing configurations.

Once there, create your first schema *(ex: admin, v1)*, set all its related configurations at once *(inputs are organized within **different tabs**)* then click on it to see its view page and start adding entities *(ex: user, book)*. To each entity you need to add attributes and define relationships if there is any *(no need to declare any  `id` or `xxx_id`  columns as those will be auto generated)*. Once your schema is complete go back to its view page and hit that **GENERATE** button.

 Built With
------------

 - [yiisoft/yii2](https://github.com/yiisoft/yii2)  -  this extension is powered by and for [Yii framework 2.0](http://www.yiiframework.com/) based applications.
 - [yiisoft/yii2-gii](https://github.com/yiisoft/yii2-gii) - the official Gii Extension for Yii framework 2.0.
 - [yiisoft/yii2-bootstrap](https://github.com/yiisoft/yii2-bootstrap) - the official Twitter Bootstrap extension for Yii framework 2.0.
 - [yii2tech/filedb](https://github.com/yii2tech/filedb) - an extension by @klimov-paul to use files for storage like db. Used to store all related GUI data in *runtime* folder by default *(storage location can be changed by setting Module::$dataPath)*.
 - [designmodo/Flat-UI](https://github.com/designmodo/Flat-UI) - a twitter bootstrap theme by @designmodo *(implemented with slight color modifications)*.
 - Code snippets from different places. Usually linked within a comment [like this one](https://github.com/tunecino/yii2-schema-builder/blob/f56d61bbcfdd94d243002e1716b9a517a3d7791a/controllers/DefaultController.php#L299) which was copied from [samdark/yii2-webshell](https://github.com/samdark/yii2-webshell) extension by @samdark.


To Do Next
-----
 
 - See what need to be done to adapt it to REST.
 - Add example on how to create a custom command list array *(Module::$commands)* returning simple commands *(either [a string or a callable function](https://github.com/tunecino/yii2-schema-builder/blob/f56d61bbcfdd94d243002e1716b9a517a3d7791a/models/Schema.php#L53))* and advanced commands involving forms to collect and store related setting inputs *(array representing a [Generator class](https://github.com/tunecino/yii2-schema-builder/blob/f56d61bbcfdd94d243002e1716b9a517a3d7791a/Module.php#L56))* .
 - Code need more care: documentation.
 - Add command for a console app or map it to existing extension *(if there are any)* that uses Faker to fill DB with some dump data.
 - Add diagram generator to preview a selected schema *(probably using [skanaar/nomnoml](https://github.com/skanaar/nomnoml) or equivalent)*
 - See if rebuild from scratch on each GENERATE could be avoided *(probably introducing a new list of commands to use [bizley/yii2-migration](https://github.com/bizley/yii2-migration) as alternative)*.

License
------------
This project is licensed under the MIT License - see the [LICENSE.md](https://raw.githubusercontent.com/tunecino/yii2-schema-builder/master/LICENSE.md) file for details.