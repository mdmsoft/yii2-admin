RBAC Manager for Yii 2
========================

Documentation
-----
- [Change Log](CHANGELOG.md).
- [Authorization Guide](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html). Important, read this first before continue.
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
```

for dev-master

```
php composer.phar require mdmsoft/yii2-admin "dev-master"
```

or add

```
"mdmsoft/yii2-admin": "~1.0"
```

to the require section of your `composer.json` file.

### Install From Archive

Download latest release from here [releases](https://github.com/mdmsoft/yii2-admin/releases) then extract to your project. 
In your application config, add path alias for this extension.

```php
return [
    ...
    'aliases' => [
        '@mdm/admin' => 'path/to/your/extracted',
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
		....
		'authManager' => [
			'class' => 'yii\rbac\PhpManager', // or use 'yii\rbac\DbManager'
		]
	],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
		'allowActions' => [
			'admin/*', // add or remove allowed actions to this list
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

To use menu manager (optional). Execute yii migration here:
```
yii migrate --migrationPath=@mdm/admin/migrations
```

If You use database (class 'yii\rbac\DbManager') to save rbac data. Execute yii migration here:
```
yii migrate --migrationPath=@yii/rbac/migrations
```

Customizing Controller
----------------------
Some controller property maybe need to change. To do that, change it via `controllerMap` property.

```php
	'modules' => [
		'admin' => [
			...,
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'path\to\models\User',
                    'idField' => 'user_id', // id field of model User
                ]
            ],
            ...
		]
		...
	],

```

Customizing Layout
------------------
As default, `module` using application layout as template. To change it, you have to set `layout` property.
This extension come with three layout that can be used, there are 'left-menu', 'right-menu' and 'top-menu'.

```php
	'modules' => [
		'admin' => [
			...,
            'layout' => 'left-menu', // default null. other avaliable value 'right-menu' and 'top-menu'
        ],
        ...
    ],
```

If you use one of them, you can also customize the menu. You can change menu label or disable it.

```php
	'modules' => [
		'admin' => [
			...,
            'layout' => 'left-menu', // default null. other avaliable value 'right-menu' and 'top-menu'
            'menus' => [
                'assignment' => [
                    'label' => 'Grand Access' // change label
                ],
                'route' => null, // disable menu
            ],
        ],
        ...
    ],
```

[screenshots](https://picasaweb.google.com/105012704576561549351/Yii2Admin?authuser=0&feat=directlink)
