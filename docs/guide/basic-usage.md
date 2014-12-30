Admin Module
============
- `layout` default to 'left-menu'. Set to null if you want use your current layout.
- `menus` Change listed menu avaliable for module.

Using module in configuration

```php
'modules' => [
    ...
    'admin' => [
        'class' => 'mdm\admin\Module',
        'layout' => 'left-menu', // it can be '@path/to/your/layout'.
        'controllerMap' => [
            'assignment' => [
                'class' => 'mdm\admin\controllers\AssignmentController',
                'userClassName' => 'app\models\User',
                'idField' => 'user_id'
            ],
            'other' => [
                'class' => 'path\to\OtherController', // add another controller
            ],
        ],
        'menus' => [
            'assignment' => [
                'label' => 'Grand Access' // change label
            ],
            'route' => null, // disable menu route 
        ]
	],
],
```

Access Control Filter
======================
Access Control Filter (ACF) is a simple authorization method that is best used by applications that only need some simple access control. 
As its name indicates, ACF is an action filter that can be attached to a controller or a module as a behavior. 
ACF will check a set of access rules to make sure the current user can access the requested action.

The code below shows how to use ACF which is implemented as `mdm\admin\components\AccessControl`:

```php
'as access' => [
    'class' => 'mdm\admin\components\AccessControl',
    'allowActions' => [
        'site/login', 
        'site/error',
    ]
]
```


Available Menu
==============
Assignment
---------
Assignment menu used for grant or revoke role to/from user.

Role and Permission
-------------------
This sesction used for manage role/permission. You can create, delete or update role/permission from this menu.
Adding and remove child of role can be doing there.

Route
-----
Route is special `permission`. Its related with application route. Because `route` is `permission`, 
so you can assign it to another `permission` or `role`. 
By default, listed route is automatic read from application structure.
Click button '>>' to save it and button '<<' to delete.

If route that you need not avaliable in list. You can create it manually. You can also create route
with extra parameter. Use `&` to sparate main route with parameter. E.g. `site/page&view=about`.

Rule
----
To using rule, define your own rule class. It should be inherited from 
[`yii\rbac\Rule`](http://www.yiiframework.com/doc-2.0/yii-rbac-rule.html).
See [Rules](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#using-rules) for more information.
Add rule to `authManager` by provide rule classname.

Using Menu
----------
See [using menu](using-menu.md)
