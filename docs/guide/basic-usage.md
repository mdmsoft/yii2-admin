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
Route is list of avaliable route of your application. It is automatic read application structure.
Click button '>>' to save it and button '<<' to delete.

Rule
----
see [Rules](http://www.yiiframework.com/doc-2.0/guide-authorization.html#using-rules).

You can used `mdm\admin\components\BizRule` or you create your own Rule class.
If you are using `mdm\admin\components\BizRule`, keep `Class Name` field blank and fill the `Expression` with
php statement that return `boolean` value.
If you are using your own class. Fill `Class Name` with you class name(full name) and anything value of `Expresion` 
will be omitted.