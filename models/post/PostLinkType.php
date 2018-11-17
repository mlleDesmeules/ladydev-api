<?php

namespace app\models\post;

use Yii;

/**
 * class PostLinkType
 *
 * @package app\models\post
 */
class PostLinkType extends PostLinkTypeBase
{
    const GITHUB = 1;

	/**
	 * @return PostLinkType[]|array
	 */
	public static function getAllEnabled()
	{
		return self::find()->isEnabled()->all();
	}
}
