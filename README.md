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
php composer.phar require mdmsoft/yii2-admin "~1.0"
or
php composer.phar require mdmsoft/yii2-admin "~2.0"
```

or for the dev-master

```
php composer.phar require mdmsoft/yii2-admin "dev-master"
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
        // for example: '@mdm/admin' => '@app/extensions/mdm/yii2-admin-2.0.0',
        ...
    ]
];
```

Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
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
        'class' => 'mdm\admin\components\AccessControl',
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
http://localhost/path/to/index.php?r=admin
http://localhost/path/to/index.php?r=admin/route
http://localhost/path/to/index.php?r=admin/permission
http://localhost/path/to/index.php?r=admin/menu
http://localhost/path/to/index.php?r=admin/role
http://localhost/path/to/index.php?r=admin/assignment
```

To use the menu manager (optional), execute the migration here:
```
yii migrate --migrationPath=@mdm/admin/migrations
```

If you use database (class 'yii\rbac\DbManager') to save rbac data, execute the migration here:
```
yii migrate --migrationPath=@yii/rbac/migrations
```

Customizing Controller
----------------------

Some controller properties may need to be adjusted to the User model of your app.
To do that, change them via `controllerMap` property. For example:

```php
    'modules' => [
        'admin' => [
            ...
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    /* 'userClassName' => 'app\models\User', */ // fully qualified class name of your User model
                    // Usually you don't need to specify it explicitly, since the module will detect it automatically
                    'idField' => 'user_id',        // id field of your User model that corresponds to Yii::$app->user->id
                    'usernameField' => 'username', // username field of your User model
                    'searchClass' => 'app\models\UserSearch'    // fully qualified class name of your User model for searching
                ]
            ],
            ...
        ]
        ...
    ],

```

Customizing Layout
------------------

By default, the admin module will use the layout specified in the application level.
In that case you have to write the menu for this module on your own.

By specifying the `layout` property, you can use one of the built-in layouts of the module:
'left-menu', 'right-menu' or 'top-menu', all equipped with the menu for this module.

```php
    'modules' => [
        'admin' => [
            ...
            'layout' => 'left-menu', // defaults to null, using the application's layout without the menu
                                     // other avaliable values are 'right-menu' and 'top-menu'
        ],
        ...
    ],
```

If you use one of them, you can also customize the menu. You can change menu label or disable it.

```php
    'modules' => [
        'admin' => [
            ...
            'layout' => 'left-menu',
            'menus' => [
                'assignment' => [
                    'label' => 'Grant Access' // change label
                ],
                'route' => null, // disable menu
            ],
        ],
        ...
    ],
```

While using a dedicated layout of the module, you may still want to have it wrapped in your application's main layout
that has your application's nav bar and your brand logo on it.
You can do it by specifying the `mainLayout` property with the application's main layout. For example:

```php
    'modules' => [
        'admin' => [
            ...
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/main.php',
            ...
        ],
        ...
    ],
```

[screenshots](https://picasaweb.google.com/105012704576561549351/Yii2Admin?authuser=0&feat=directlink)
