Basic Configuration
-------------------

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
http://localhost/path/to/index.php?r=admin/user
```

To use the menu manager (optional), execute the migration here:
```
yii migrate --migrationPath=@mdm/admin/migrations
```

If you use database (class 'yii\rbac\DbManager') to save rbac data, execute the migration here:
```
yii migrate --migrationPath=@yii/rbac/migrations
```

Customizing Assignment Controller
---------------------------------

Assignment controller properties may need to be adjusted to the User model of your app.
To do that, change them via `controllerMap` property. For example:

```php
    'modules' => [
        'admin' => [
            ...
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    /* 'userClassName' => 'app\models\User', */
                    'idField' => 'user_id',
                    'usernameField' => 'username',
                    'fullnameField' => 'profile.full_name',
                    'extraColumns' => [
                        [
                            'attribute' => 'full_name',
                            'label' => 'Full Name',
                            'value' => function($model, $key, $index, $column) {
                                return $model->profile->full_name;
                            },
                        ],
                        [
                            'attribute' => 'dept_name',
                            'label' => 'Department',
                            'value' => function($model, $key, $index, $column) {
                                return $model->profile->dept->name;
                            },
                        ],
                        [
                            'attribute' => 'post_name',
                            'label' => 'Post',
                            'value' => function($model, $key, $index, $column) {
                                return $model->profile->post->name;
                            },
                        ],
                    ],
                    'searchClass' => 'app\models\UserSearch'
                ],
            ],
            ...
        ]
        ...
    ],

```

- Required properties
    - **userClassName** Fully qualified class name of your User model  
        Usually you don't need to specify it explicitly, since the module will detect it automatically
    - **idField** ID field of your User model  
        The field that corresponds to Yii::$app->user->id.  
        The default value is 'id'.
    - **usernameField** User name field of your User model  
        The default value is 'username'.
- Optional properties
    - **fullnameField** The field that specifies the full name of the user used in "view" page.  
        This can either be a field of the user model or of a related model (e.g. profile model).  
        When the field is of a related model, the name should be specified with a dot-separated notation (e.g. 'profile.full_name').  
        The default value is null.
    - **extraColumns** The definition of the extra columns used in the "index" page  
        This should be an array of the definitions of the grid view columns.  
        You may include the attributes of the related models as you see in the example above.  
        The default value is an empty array.
    - **searchClass** Fully qualified class name of your model for searching the user model  
        You have to supply the proper search model in order to enable the filtering and the sorting by the extra columns.  
        The default value is null.


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

More...
---------------

- [**Basic Usage...**](basic-usage.md)
- [**User Management**](user-management.md)
- [**Using Menu**](using-menu.md)
