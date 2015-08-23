RBAC Manager for Yii 2
======================

Documentation
-------------

- [Change Log](CHANGELOG.md).
- [Authorization Guide](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html). Important, read this first before you continue.
- [Basic Usage](docs/guide/basic-usage.md).
- [Using Menu](docs/guide/using-menu.md).
- [Api](http://mdmsoft.github.io/yii2-admin/index.html)

Installation
------------

### Install With Composer

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require mdmsoft/yii2-admin "~3.0"
```

or for the dev-master

```
php composer.phar require mdmsoft/yii2-admin "dev-3.master"
```

Or, you may add

```
"mdmsoft/yii2-admin": "~2.0"
```

to the require section of your `composer.json` file and execute `php composer.phar update`.

### Install From the Archive

Download the latest release from here [releases](https://github.com/mdmsoft/yii2-admin/releases), then extract it to your project.
In your application config, add the path alias for this extension.

```php
return [
    ...
    'aliases' => [
        '@mdm/admin' => 'path/to/your/extracted',
        // for example: '@mdm/admin' => '@app/extensions/mdm/yii2-admin-3.0.0',
        ...
    ]
];
```

Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => [
        'admin', // required
        ...
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            ...
        ]
        ...
    ],
    ...
    'components' => [
        ...
        'authManager' => [
            'class' => 'yii\rbac\PhpManager', // or use 'yii\rbac\DbManager'
        ]
    ],
    'as access' => [
        'class' => 'mdm\admin\classes\AccessControl',
        'allowActions' => [
            'site/*',
            'admin/*',
            'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
    ],
];
```
See [Yii RBAC](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#role-based-access-control-rbac) for more detail.
You can then access Auth manager through the following URL:

```
http://localhost/path/to/index.php/admin
http://localhost/path/to/index.php/admin#/route
http://localhost/path/to/index.php/admin#/permission
http://localhost/path/to/index.php/admin#/menu
http://localhost/path/to/index.php/admin#/role
http://localhost/path/to/index.php/admin#/assignment
```

To use the menu manager (optional), execute the migration here:
```
yii migrate --migrationPath=@mdm/admin/migrations
```

If you use database (class 'yii\rbac\DbManager') to save rbac data, execute the migration here:
```
yii migrate --migrationPath=@yii/rbac/migrations
```

Upgrade From 2.x
----------------

- All classes under namespace `mdm\admin\components` moved to `mdm\admin\classes`.
So you must change it.
- Version 3.x only work with enable prety url.
- You must add module to application bootstrap in config.