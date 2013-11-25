<?php

namespace mdm\auth\models;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'password', 'email'], 'required'],
			[['username', 'password'], 'string', 'max' => 32],
			[['email'], 'string', 'max' => 64]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getAuthAssignment()
	{
		return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getItemNames()
	{
		return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('tbl_auth_assignment', ['user_id' => 'id']);
	}

	public static function findIdentity($id)
	{
		return self::find($id);
	}

	public static function findByUsername($username)
	{
		return self::find(['username' => $username]);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getAuthKey()
	{
		return md5('AUTHKEY#' . $this->password);
	}

	public function validateAuthKey($authKey)
	{
		return md5('AUTHKEY#' . $this->password) === $authKey;
	}

	public function validatePassword($password)
	{
		return $this->password === md5($password);
	}

}
