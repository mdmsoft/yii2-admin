Admin Module
------------
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
---------------------
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

Filter ActionColumn Buttons
---------------------------
When you use `GridView`, you can also filtering button visibility.
```php
use mdm\admin\components\Helper;

'columns' => [
    ...
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => Helper::filterActionColumn('{view}{delete}{posting}'),
    ]
]
```
It will check authorization access of button and show or hide it.

To check access for route, you can use
```php
use mdm\admin\components\Helper;

if(Helper::checkRoute('delete')){
    echo Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->name], [
        'class' => 'btn btn-danger',
        'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
        'data-method' => 'post',
    ]);
}

```

More...
---------------

- [**User Management**](user-management.md)
- [**Using Menu**](using-menu.md)
- [**Basic Configuration**](configuration.md)
