<?php

namespace app\modules\v1\admin\models\post;

use app\models\post\PostLinkType;

/**
 * Class PostLinkTypeEx
 *
 * @package app\modules\v1\admin\models\post
 */
class PostLinkTypeEx extends PostLinkType
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition="PostLinkType",
	 *
	 *     @SWG\Property(property="id",          type="integer", description="Link type Identifier"),
	 *     @SWG\Property(property="name",        type="string",  description="Link type name"),
	 *     @SWG\Property(property="description", type="string",  description="Link type description"),
	 * )
	 */
	public function fields()
	{
		return [
			"id",
			"name",
			"description",
		];
	}
}