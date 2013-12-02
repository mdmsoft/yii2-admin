<?php

namespace mdm\auth\models;

use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $password_hash
 * @property string $email
 */
class User extends ActiveRecord implements IdentityInterface
{

	public $password;
	public $retypePassword;
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
			[['username', 'email'], 'filter', 'filter' => 'trim'],
			[['username', 'email'], 'required'],
			[['username','password'], 'string', 'min' => 6, 'max' => 32],

			['email', 'filter', 'filter' => 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'unique', 'message' => 'This email address has already been taken.', 'on' => 'signup'],
			['email', 'exist', 'message' => 'There is no user with such email.', 'on' => 'requestPasswordResetToken'],

			['password', 'required'],
			['password', 'string', 'min' => 6],
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
			'retypePassword' => 'Retype Password',
			'email' => 'Email',
		];
	}

	public function beforeSave($insert)
	{
		if(parent::beforeSave($insert)){
			if (($this->isNewRecord || $this->getScenario() === 'resetPassword') && !empty($this->password)) {
				$this->password_hash = Security::generatePasswordHash($this->password);
			}
			return true;
		}
		return false;
	}

	public function getRoles()
	{
		$roles = \Yii::$app->authManager->getItems($this->id);
		return implode(', ', array_keys($roles));
	}

	// Inherited from IdentityInterface
	
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
		return md5('AUTHKEY#' . $this->password_hash);
	}

	public function validateAuthKey($authKey)
	{
		return md5('AUTHKEY#' . $this->password_hash) === $authKey;
	}

	public function validatePassword($password)
	{
		return Security::validatePassword($password, $this->password_hash);
	}

}
