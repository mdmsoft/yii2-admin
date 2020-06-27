RBAC Manager for Yii 2
======================
GUI manager for RBAC (Role Base Access Control) Yii2. Easy to manage authorization of user :smile:.

[![Latest Unstable Version](https://poser.pugx.org/mdmsoft/yii2-admin/v/unstable)](https://packagist.org/packages/mdmsoft/yii2-admin)
[![Total Downloads](https://poser.pugx.org/mdmsoft/yii2-admin/downloads.png)](https://packagist.org/packages/mdmsoft/yii2-admin)
[![Daily Downloads](https://poser.pugx.org/mdmsoft/yii2-admin/d/daily)](https://packagist.org/packages/mdmsoft/yii2-admin)
[![License](https://poser.pugx.org/mdmsoft/yii2-admin/license)](https://packagist.org/packages/mdmsoft/yii2-admin)
[![Reference Status](https://www.versioneye.com/php/mdmsoft:yii2-admin/reference_badge.svg)](https://www.versioneye.com/php/mdmsoft:yii2-admin/references)
[![Build Status](https://img.shields.io/travis/mdmsoft/yii2-admin.svg)](http://travis-ci.org/mdmsoft/yii2-admin)
[![Dependency Status](https://www.versioneye.com/php/mdmsoft:yii2-admin/dev-master/badge.png)](https://www.versioneye.com/php/mdmsoft:yii2-admin/dev-master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mdmsoft/yii2-admin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mdmsoft/yii2-admin/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/mdmsoft/yii2-admin.svg)](https://codeclimate.com/github/mdmsoft/yii2-admin)

Documentation
-------------
> **Important: If you install version 3.x, please see [this readme](https://github.com/mdmsoft/yii2-admin/blob/3.master/README.md#upgrade-from-2x).**


- [Change Log](CHANGELOG.md).
- [Authorization Guide](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html). Important, read this first before you continue.
- [Basic Configuration](docs/guide/configuration.md)
- [Basic Usage](docs/guide/basic-usage.md).
- [User Management](docs/guide/user-management.md).
- [Using Menu](docs/guide/using-menu.md).
- [Api](https://mdmsoft.github.io/yii2-admin/index.html).

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
php composer.phar require mdmsoft/yii2-admin "2.x-dev"
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

[**More...**](docs/guide/configuration.md)

[screenshots](https://goo.gl/r8RizT)
