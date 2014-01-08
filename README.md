Auth Extension for Yii 2
========================


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require mdm-yii2/mdm-admin "*"
```

or add

```
"mdm-yii2/mdm-admin": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
	'modules' => [
		'admin' => 'mdm\admin\Module',
		...
	],
	...
	'as access' => 'mdm\admin\components\AccessControl',
];
```

You can then access Auth manager through the following URL:

```
http://localhost/path/to/index.php?r=admin
```