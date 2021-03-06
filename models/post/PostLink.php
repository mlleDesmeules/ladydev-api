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
     * type of link doesn't already exists. After that the link will be created.
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

	    return self::buildSuccess([ "post_id" => $postId, "post_link_type" => $linkType ]);
    }

	/**
	 * @param integer $postId
	 * @param integer $postType
	 *
	 * @return array
	 * @throws ErrorException
	 */
    public static function deleteLink($postId, $postType)
    {
	    // if the link doesn't exists, throw an error
	    if (!self::linkExists($postId, $postType)) {
		    throw new ErrorException(self::ERR_LINK_NOT_EXISTS);
	    }

	    // if the post for this link is published, then throw an error
	    if (Post::isPublished($postId)) {
		    throw new ErrorException(Post::ERR_POST_PUBLISHED);
	    }

	    $model = self::find()->byPost($postId)->byType($postType)->one();

	    try {
		    if (!$model->delete()) {
			    throw new ErrorException(self::ERR_ON_DELETE);
		    }
	    } catch (\Throwable $e) {
		    throw new ErrorException(self::ERR_ON_DELETE);
	    }

	    return self::buildSuccess([]);
    }

	/**
	 * @param $postId
	 *
	 * @return PostLinkQuery
	 */
	public static function getByPost($postId)
	{
		return self::find()->byPost($postId);
	}

	/**
	 * @param $postId
	 * @param $linkType
	 *
	 * @return PostLinkQuery
	 */
	public static function getByPostType($postId, $linkType)
	{
		return self::find()->byPost($postId)->byType($linkType);
	}

	/**
	 * Update Post Link
	 *
	 * This method will update a specific post link. First, a verification will be made
	 * to make sure that the post link exists, then the model will be updated.
	 *
	 * @param integer $postId
	 * @param integer $postType
	 * @param self|array $data
	 *
	 * @return array
	 * @throws yii\base\ErrorException
	 */
    public static function updateLink($postId, $postType, $data)
    {
	    // if the link doesn't exists, throw an error
	    if (!self::linkExists($postId, $postType)) {
		    throw new ErrorException(self::ERR_LINK_NOT_EXISTS);
	    }

	    // find the post link
	    $model = self::find()->byPost($postId)->byType($postType)->one();

	    $model->link = ArrayHelperEx::getValue($data, "link", $model->link);

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
}

// EOF