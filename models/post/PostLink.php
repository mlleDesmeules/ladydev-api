<?php

namespace app\models\post;

use app\helpers\ArrayHelperEx;
use yii\base\ErrorException;
use Yii;

/**
 * class PostLink
 *
 * @package app\models\post
 */
class PostLink extends PostLinkBase
{
    /**
     * Create a post link
     *
     * This method will create a link for a specific post. It will first make sure
     * the ID passed in parameter exists, along with the post link type and that this
     * type of link doesn't already exists. After than the link will be created.
     *
     * @param integer    $postId
     * @param self|array $data
     *
     * @return array
     * @throws yii\base\ErrorException
     */
    public static function createLink($postId, $data)
    {
        //  if post doesn't exists, then throw an error
		if ( !Post::idExists($postId) ) {
            throw new ErrorException(self::ERR_POST_NOT_FOUND);
        }

        $linkType = ArrayHelperEx::getValue($data, "post_link_type");

        //  if the post link type doesn't exists, then throw an error
        if (!PostLinkType::idExists($linkType)) {
            throw new ErrorException(self::ERR_LINK_TYPE_NOT_FOUND);
        }

        //  check if link type already exists for post, then throw an error
        if (self::linkExists($postId, $linkType)) {
            throw new ErrorException(self::ERR_LINK_EXISTS);
        }

        //  create the new post link
        $model = new self();

        $model->post_id        = $postId;
        $model->post_link_type = $linkType;
        $model->link           = ArrayHelperEx::getValue($data, "link");

        //  if the model isn't valid, then return all errors
        if (!$model->validate()) {
            return self::buildError($model->getErrors());
        }

        //  if the model couldn't be saved, then throw an error
        if (!$model->save()) {
            throw new ErrorException(self::ERR_ON_SAVE);
        }

        return self::buildSuccess([]);
    }

    /**
     * todo: add comment
     * todo: implement
     */
    public static function deleteLink($postId, $postType)
    {

    }

    /**
     * todo: add comment
     * todo: implement
     */
    public static function updateLink($postId, $postType, $data)
    {

    }
}