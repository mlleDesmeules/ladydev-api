<?php

namespace app\modules\v1\admin\models\post;

use app\helpers\ArrayHelperEx;
use app\models\post\PostLink;
use yii\base\ErrorException;

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
	 * @param PostLink|array $data
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
	 * @param $linkType
	 *
	 * @return PostLink|array|null
	 */
	public static function getLink($postId, $linkType)
	{
		return self::find()->byPost($postId)
		           ->byType($linkType)
		           ->withType()
		           ->one();
	}
}

//  EOF