<?php

namespace app\modules\v1\models\post;


use app\models\post\PostLink;

/**
 * Class PostLinkEx
 *
 * @package app\modules\v1\models\post
 */
class PostLinkEx extends PostLink
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition="PostLink",
	 *
	 *     @SWG\Property(property="type", type="integer", description="Link type ID"),
	 *     @SWG\Property(property="link", type="string", description="Complete link"),
	 * )
	 */
	public function fields()
	{
		return [
			"type" => "post_link_type",
			"link",
		];
	}
}