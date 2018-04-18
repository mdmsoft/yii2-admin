User Management
===============

For `basic application template` that want to have user stored in database.
To use this feature, create required table by execute migration.
```
./yii migrate --migrationPath=@mdm/admin/migrations
```
Then, change config of user component
```php
    'components' => [
        ...
        'user' => [
            'identityClass' => 'mdm\admin\models\User',
            'loginUrl' => ['admin/user/login'],
        ]
    ]
```
Then you can access this menu at `index.php?r=admin/user`.

Signup User
-----------
```
http://localhost/myapp/index.php?r=admin/user/signup
```
Default registered user has status `ACTIVE`, mean user can login without activation needed.
To change that, you can change at config/params.php
```php
// config/params.php

return [
    ...
    'mdm.admin.configs' => [
        'defaultUserStatus' => 0, // 0 = inactive, 10 = active
    ]
];
```

Login Page
----------
Login page can access at `index.php?r=admin/user/login`

More...
---------------

- [**Basic Usage**](basic-usage.md)
- [**Using Menu**](using-menu.md)
- [**Basic Configuration**](configuration.md)
