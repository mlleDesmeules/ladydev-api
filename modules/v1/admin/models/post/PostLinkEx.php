<?php

namespace app\modules\v1\admin\models\post;

use app\helpers\ArrayHelperEx;
use app\models\post\PostLink;
use yii\base\ErrorException;

/**
 * class PostLinkEx
 *
 * @package app\models\v1\admin\models\post
 *
 * @SWG\Definition(definition="PostLinkList", type="array", @SWG\Items(ref="#/definitions/PostLink"))
 */
class PostLinkEx extends PostLink
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *      definition="PostLink",
	 *
	 *      @SWG\Property(property="post_id",   type="integer", description="Post ID for this link"),
	 *      @SWG\Property(property="link_type", type="integer", description="Link Type ID of this link"),
	 *      @SWG\Property(property="link",      type="string", description="Complete URL"),
	 * )
	 */
	public function fields()
	{
		return [
			"post_id",
			"link_type" => "post_link_type",
			"link",
		];
	}

	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *      definition="PostLinkForm",
	 *
	 *      @SWG\Property(property="post_id", type="integer", description="ID of the post for the link"),
	 *      @SWG\Property(property="post_type_link", type="integer", description="ID of the type of link"),
	 *      @SWG\Property(property="link", type="string", description="link itself"),
	 * )
	 */
	public function rules()
	{
		return parent::rules();
	}

	public static function buildFormError($errors, $withStatus = false)
	{
		$result = self::buildError([
			"short_message" => self::ERR_MODEL_INVALID,
			"message"       => self::getErrorMessage(self::ERR_MODEL_INVALID),
			"form_errors"   => $errors,
		]);

		return ($withStatus) ? $result : $result[ "error" ];
	}

	/**
	 * Create Link
	 *
	 * This method will create the post link itself, then handle and process the error
	 * result to be returned by the API.
	 *
	 * @param int $postId
	 * @param self|array $data
	 *
	 * @return array
	 */
	public static function createLink($postId, $data)
	{
		try {
			$result = parent::createLink($postId, $data);

			if ($result[ "status" ] === PostLinkEx::ERROR) {
				$result = self::buildFormError($result[ "error" ], true);
			}

		} catch (ErrorException $e) {
			$result = self::buildError([
				"message"       => self::getErrorMessage($e->getMessage()),
				"short_message" => $e->getMessage(),
			]);
		}

		return $result;
	}

	/**
	 * @param $postId
	 *
	 * @return self[]|array
	 */
	public static function getByPost($postId)
	{
		return parent::getByPost($postId)->withType()->all();
	}

	/**
	 * @param int $postId
	 * @param int $linkType
	 *
	 * @return self
	 */
	public static function getByPostType($postId, $linkType)
	{
		return parent::getByPostType($postId, $linkType)
		             ->withType()
		             ->one();
	}

	/**
	 * Get Error Message
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	static public function getErrorMessage($key)
	{
		$list = [
			self::ERR_POST_NOT_FOUND      => "",
			self::ERR_LINK_TYPE_NOT_FOUND => "",
			self::ERR_LINK_NOT_EXISTS     => "",
			self::ERR_LINK_EXISTS         => "",
			self::ERR_ON_SAVE             => "",
			self::ERR_ON_DELETE           => "",
			self::ERR_MODEL_INVALID       => "",

			PostEx::ERR_POST_PUBLISHED => "",
		];

		return ArrayHelperEx::getValue($list, $key, $key);
	}

	/**
	 * Update Link
	 *
	 * This method will call the parent method to update the post link and
	 * catch any exception thrown. Then transform them into an error array
	 * and return it.
	 *
	 * @param int $postId
	 * @param int $postType
	 * @param self|array $data
	 *
	 * @return array
	 */
	public static function updateLink($postId, $postType, $data)
	{
		try {
			$result = parent::updateLink($postId, $postType, $data);

			if ($result[ "status" ] === PostLinkEx::ERROR) {
				$result = self::buildFormError($result[ "error" ], true);
			}
		} catch (ErrorException $e) {
			$result = self::buildError([
				"message"       => self::getErrorMessage($e->getMessage()),
				"short_message" => $e->getMessage(),
			]);
		}

		return $result;
	}
}

//  EOF