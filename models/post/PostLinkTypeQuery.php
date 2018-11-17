<?php

namespace app\models\post;

/**
 * Class PostLinkTypeQuery
 *
 * @package app\models\post
 *
 * @see     PostLinkType
 */
class PostLinkTypeQuery extends \yii\db\ActiveQuery
{
	/**
	 * @inheritdoc
	 * @return PostLinkType[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PostLinkType|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @return PostLinkTypeQuery
	 */
	public function isEnabled()
	{
		return $this->andWhere([ "is_enabled" => PostLinkType::ENABLED ]);
	}
}