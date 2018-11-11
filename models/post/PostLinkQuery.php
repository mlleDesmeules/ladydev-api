<?php

namespace app\models\post;

use app\modules\v1\admin\models\post\PostLinkEx;

/**
 * This is the ActiveQuery class for [[PostLink]].
 *
 * @see PostLink
 */
class PostLinkQuery extends \yii\db\ActiveQuery
{
	/**
	 * @inheritdoc
	 * @return PostLink[]|PostLinkEx[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PostLink|PostLinkEx|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param integer $id
	 *
	 * @return $this
	 */
	public function byPost($id)
	{
		return $this->andWhere([ "post_id" => $id ]);
	}

	/**
	 * @param integer $id
	 *
	 * @return $this
	 */
	public function byType($id)
	{
		return $this->andWhere([ "post_link_type" => $id ]);
	}

	public function withType()
	{
		return $this->joinWith("postLinkType");
	}
}
