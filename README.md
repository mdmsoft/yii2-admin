RBAC Manager for Yii 2
========================

Dosc:
-----
- [change log](CHANGELOG.md).
- [Basic Usage](docs/guide/basic-usage.md).
- [Using Menu](docs/guide/using-menu.md).

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require mdmsoft/yii2-admin "*"
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
	'bootstrap' => [
		'admin',
		...
	],
	'modules' => [
		'admin' => [
			'class' => 'mdm\admin\Module',
			'allowActions' => [
				'admin/*', // add or remove allowed actions to this list
			]
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
];
```

See [Yii RBAC](http://www.yiiframework.com/doc-2.0/guide-authorization.html#role-based-access-control-rbac) for more detail.
You can then access Auth manager through the following URL:

```
http://localhost/path/to/index.php?r=admin
```

To use menu manager (optional). Execute yii migration
```
yii migrate --migrationPath=@mdm/admin/migration
```

[screenshots](https://picasaweb.google.com/105012704576561549351/Yii2Admin?authuser=0&feat=directlink)