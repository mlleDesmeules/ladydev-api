<?php

namespace app\modules\v1\admin\controllers\post;

use app\modules\v1\admin\components\ControllerAdminEx;
use app\modules\v1\admin\models\post\PostLinkEx;
use app\modules\v1\admin\models\post\PostEx;

/**
 * Class LinkController
 *
 * @package app\modules\v1\admin\controller\post
 */
class LinkController extends ControllerAdminEx
{
	/** @inheritdoc */
	public $corsMethods = [ "OPTIONS", "POST", "PUT", "DELETE" ];

	/** @inheritdoc */
	protected function verbs()
	{
		return [
			"index"  => [ "OPTIONS", "GET" ],
			"view"   => [ "OPTIONS", "GET" ],
			"create" => [ "OPTIONS", "POST" ],
			"update" => [ "OPTIONS", "PUT" ],
			"delete" => [ "OPTIONS", "DELETE" ],
		];
	}

	public function actionIndex($postId)
	{
		var_dump($postId);
		die();
	}

	public function actionView($postId, $postType)
	{
		var_dump($postId);
		var_dump($postType);
		die();
	}

	/**
	 * Create a Post Link
	 *
	 * @param int $postId
	 *
	 * @return array
	 * @throws \yii\base\Exception
	 */
	public function actionCreate($postId)
	{
		//  return error if the post wasn't found
		if (!PostEx::idExists($postId)) {
			return $this->error(404, [
				"short_message" => PostLinkEx::ERR_POST_NOT_FOUND,
				"message"       => PostLinkEx::getErrorMessage(PostLinkEx::ERR_POST_NOT_FOUND),
			]);
		}

		//  create form with data received in request
		$form = new PostLinkEx();

		$form->setAttributes($this->request->getBodyParams());
		$form->post_id = $postId;

		//  validate the data received and return the error if there is one
		if (!$form->validate()) {
			return $this->error(422, PostLinkEx::buildFormError($form->getErrors()));
		}

		//  create the post link
		$result = PostLinkEx::createLink($postId, $form);

		if ($result[ "status" ] === PostLinkEx::ERROR) {
			return $this->handleErrors($result[ "error" ]);
		}

		// returned the updated complete post
		return $this->createdResult(PostLinkEx::getLink($result[ "post_id" ], $result[ "post_link_type" ]));
	}

	/**
	 * todo: implement update
	 * todo: add comment
	 */
	public function actionUpdate($postId, $postType)
	{
		var_dump($postId);
		var_dump($postType);
		die();
	}

	/**
	 * todo: implement update
	 * todo: add comment
	 */
	public function actionDelete($postId, $postType)
	{
		var_dump($postId);
		var_dump($postType);
		die();
	}

	/**
	 * Handle Errors
	 *
	 * This method will return the correct error response code depending on the error
	 * passed in parameter.
	 *
	 * @param array $error
	 *
	 * @return array
	 */
	private function handleErrors($error)
	{
		switch ($error[ "short_message" ]) {
			case PostLinkEx::ERR_POST_NOT_FOUND:
				// no break
			case PostLinkEx::ERR_LINK_TYPE_NOT_FOUND:
				return $this->error(404, $error);

			case PostLinkEx::ERR_MODEL_INVALID:
				return $this->unprocessableResult($error);

			case PostLinkEx::ERR_ON_SAVE:
				//  no break;
			default:
				return $this->error(500, $error);
		}
	}
}