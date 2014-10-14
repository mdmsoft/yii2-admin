Assigment
---------
Assigment menu used for grant or revoke role to/from user.

Role
----
Role menu used for manage role. You can create, delete or update role from this menu.
Adding and remove child of role can be doing there.

Permision
---------


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
see [Rules](http://www.yiiframework.com/doc-2.0/guide-authorization.html#using-rules).
