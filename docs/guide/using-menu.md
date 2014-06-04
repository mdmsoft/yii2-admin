Using Menu
----------

Menu manager used for build hierarchical menu. This is automatically look of user 
role and permision then return menus that he has access.

```php
use mdm\admin\components\AccessHelper;
use yii\bootstrap\Nav;

echo Nav::widget([
    'items' => AccessHelper::getAssignedMenu(Yii::$app->user->id)
]);

```
Return of `mdm\admin\components\AccessHelper::getAssignedMenu()` has default format like:
```php
[
    [
        'label' => $menu['name'], 
        'url' => [$menu['route']],
        'items' => [
			[
				'label' => $menu['name'], 
				'url' => [$menu['route']]
            ],
            ....
        ]
    ],
    [
        'label' => $menu['name'], 
        'url' => [$menu['route']],
        'items' => [
			[
				'label' => $menu['name'], 
				'url' => [$menu['route']]
            ]
        ]
    ],
    ....
]
```
where `$menu` variable corresponden with a record of table `menu`. You can customize 
return format of `mdm\admin\components\AccessHelper::getAssignedMenu()` by provide a callback to this methode.
The callback must have format `function($menu){}`. E.g:
```php
$callback = function($menu){
    $data = eval($menu['data']);
    return [
        'label' => $menu['name'], 
        'url' => [$menu['route']],
        'options' => $data,
        'items' => [
			[
				'label' => $menu['name'], 
				'url' => [$menu['route']]
            ]
        ]
    ]
}

$items = AccessHelper::getAssignedMenu(Yii::$app->user->id,$callback);
```
Default result is get from `cache`. If you want to force recalculate, provide boolean `true` as third parameter.