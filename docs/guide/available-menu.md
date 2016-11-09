Assignment
---------
Assignment menu used for grant or revoke role to/from user.

![Assignment](/docs/images/image01.png)
![Assignment](/docs/images/image02.png)

Route
-----
Route is special `permission`. Its related with application route. Because `route` is `permission`,
so you can assign it to another `permission` or `role`.
By default, listed route is automatic read from application structure.
Click button '>>' to save it and button '<<' to delete.

If route that you need not available in list. You can create it manually. You can also create route
with extra parameter. Use `&` to sparate main route with parameter. E.g. `site/page&view=about`.

![Route](/docs/images/image03.png)

Role and Permission
-------------------
This sesction used for manage role/permission. You can create, delete or update role/permission from this menu.
Adding and remove child of role can be doing there.

![Role](/docs/images/image04.png)
![Create Role](/docs/images/image05.png)
![Add Child](/docs/images/image06.png)
![Update Permission](/docs/images/image07.png)

Rule
----
To using rule, define your own rule class. It should be inherited from
[`yii\rbac\Rule`](http://www.yiiframework.com/doc-2.0/yii-rbac-rule.html).
See [Rules](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#using-rules) for more information.
Add rule to `authManager` by provide rule classname.

![Rule](/docs/images/image08.png)

More...
--------

- [**User Management**](user-management.md)
- [**Using Menu**](using-menu.md)
- [**Basic Configuration**](configuration.md)

