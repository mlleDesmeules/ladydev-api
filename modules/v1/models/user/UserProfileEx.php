<?php

namespace app\modules\v1\models\user;

use app\models\user\UserProfile;
use app\models\user\UserProfileLang;
use app\modules\v1\models\LangEx;

/**
 * Class UserProfileEx
 *
 * @package app\modules\v1\models\user
 *
 * @property UserProfileLang $profileLang
 */
class UserProfileEx extends UserProfile
{
    /** @inheritdoc */
    public function fields()
    {
        return [
            "id" => "user_id",
            "fullname" => function (self $model) { return $model->getFullname(); },
            "firstname",
            "lastname",
            "picture"   => function (self $model) { return ($model->hasProfilePicture()) ? $model->file->getFullPath() : ""; },
            "biography" => function (self $model) { return $model->profileLang->biography; },
            "job_title" => function (self $model) { return $model->profileLang->job_title; },
        ];
    }

    /**
     *
     * @return self|null
     */
    public static function getAuthor()
    {
        return self::find()->user(1)->one();
    }

    /**
     * Return the user's full name.
     *
     * @return string
     */
    public function getFullname ()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Verify if the user has a profile picture 
     */
    public function hasProfilePicture() 
    {
        return !is_null($this->file);
    }
}
