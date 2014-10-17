RBAC Manager for Yii 2
========================

Documentation
-----
- [Change Log](CHANGELOG.md).
- [Basic Usage](docs/guide/basic-usage.md).
- [Using Menu](docs/guide/using-menu.md).
- [Api](http://mdmsoft.github.io/yii2-admin/index.html)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require mdmsoft/yii2-admin "*"
```

for dev-master

```
php composer.phar require mdmsoft/yii2-admin "dev-master"
```

or add

```
"mdmsoft/yii2-admin": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
	'modules' => [
		'admin' => [
			'class' => 'mdm\admin\Module',
            'layout' => 'left-menu', // avaliable value 'left-menu', 'right-menu' and 'top-menu'
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'app\models\User',
                    'idField' => 'id'
                ]
            ],
            'menus' => [
                'assignment' => [
                    'label' => 'Grand Access' // change label
                ],
                'route' => null, // disable menu
            ],
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

[screenshots](https://picasaweb.google.com/105012704576561549351/Yii2Admin?authuser=0&feat=directlink)
