<?php

namespace app\modules\v1\models\user;

use app\models\user\User;

/**
 * Class UserEx
 *
 * @package app\modules\v1\models
 */
class UserEx extends User
{
	/** @inheritdoc */
	public function getUserProfile()
	{
		return $this->hasOne(UserProfileEx::className(), [ "user_id"  => "id" ]);
	}

	public function fields ()
	{
		return [
			"id",
			"fullname"  => function ( self $model ) { return $model->userProfile->getFullname(); },
			"firstname" => function ( self $model ) { return $model->userProfile->firstname; },
			"lastname"  => function ( self $model ) { return $model->userProfile->lastname; },
			"picture"   => function ( self $model ) { return ($model->userProfile->hasProfilePicture()) ? $model->userProfile->file->getFullPath() : ""; },
		];
	}
}
