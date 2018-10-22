<?php

namespace app\modules\v1\admin\models\post;

use app\models\post\PostLink;

/**
 * class PostLinkEx
 *
 * @package app\models\v1\admin\models\post
 */
class PostLinkEx extends PostLink
{
    /**
     * @inheritdoc
     *
     * @SWG\Definition(
     *      definition="PostLink",
     *
     *      @SWG\Property(property="name", type="string", description="Name of the type of link"),
     *      @SWG\Property(property="url", type="string", description="Complete URL"),
     * )
     */
    public function fields()
    {
        return [
            "name" => "postLinkType.name",
            "url"  => "link",
        ];
    }
}