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
	 * This method will get the data sent in the request, validate it before trying to
	 * create the post link. An error will be returned if at any point there is an issue
	 * with the creation or in case of success, the post link will be returned.
	 *
	 * @param int $postId
	 *
	 * @return array
	 * @throws \yii\base\Exception
	 *
	 * @SWG\Post(
	 *     path="posts/:postId/links",
	 *     tags={"Posts", "Post Links"},
	 *
	 *     summary="Create a post link",
	 *     description="Create a link for a specific post",
	 *
	 *     @SWG\Parameter(name="postId", in="path", type="integer", required=true),
	 *
	 *     @SWG\Response(response=201, description="post link created", @SWG\Schema(ref="#/definitions/PostLink")),
	 *     @SWG\Response(response=401, description="user can't be authenticated", @SWG\Schema(ref="#/definitions/GeneralError")),
	 *     @SWG\Response(response=404, description="post or link type not found", @SWG\Schema(ref="#/definitions/GeneralError")),
	 *     @SWG\Response(response=422, description="invalid data received", @SWG\Schema(ref="#/definitions/UnprocessableError")),
	 *     @SWG\Response(response=500, description="server error", @SWG\Schema(ref="#/definitions/GeneralError")),
	 * )
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
		$form = $this->requestForm($postId);

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
	 * Update post link
	 *
	 * This method will get the data sent in the request, validate it before trying to
	 * update the post link. An error will be returned if at any point there is an issue
	 * with the update of the link or in case of success, the link itself will be returned.
	 *
	 * @param int $postId
	 * @param int $linkType
	 *
	 * @return PostLinkEx
	 * @throws \yii\base\InvalidConfigException
	 *
	 * @SWG\Put(
	 *     path="posts/:postId/links/:linkType",
	 *     tags={"Posts", "Post Links"},
	 *
	 *     summary="Update a post link",
	 *     description="Update a existing link for a given post",
	 *
	 *     @SWG\Parameter(name="postId", in="path", type="integer", required=true),
	 *     @SWG\Parameter(name="linkType", in="path", type="integer", required=true),
	 *
	 *     @SWG\Response(response=200, description="updated post link", @SWG\Schema(ref="#/definitions/PostLink")),
	 *     @SWG\Response(response=401, description="user can't be authenticated", @SWG\Schema(ref="#/definitions/GeneralError")),
	 *     @SWG\Response(response=404, description="post or link type not found", @SWG\Schema(ref="#/definitions/GeneralError")),
	 *     @SWG\Response(response=422, description="invalid data received", @SWG\Schema(ref="#/definitions/UnprocessableError")),
	 *     @SWG\Response(response=500, description="server error", @SWG\Schema(ref="#/definitions/GeneralError")),
	 * )
	 */
	public function actionUpdate($postId, $linkType)
	{
		//  return error if the post link doesn't exists
		if (!PostLinkEx::linkExists($postId, $linkType)) {
			return $this->error(404, [
				"short_message" => PostLinkEx::ERR_LINK_NOT_EXISTS,
				"message"       => PostLinkEx::getErrorMessage(PostLinkEx::ERR_LINK_NOT_EXISTS),
			]);
		}

		//  create form with data received from request
		$form = $this->requestForm($postId, $linkType);

		//  validate the data received and return the error if there is one
		if (!$form->validate()) {
			return $this->error(422, PostLinkEx::buildFormError($form->getErrors()));
		}

		//  update the post link
		$result = PostLinkEx::updateLink($postId, $linkType, $form);

		//  in case of error on update, return it
		if ($result[ "status" ] === PostLinkEx::ERROR) {
			return $this->handleErrors($result[ "error" ]);
		}

		//  return updated post link
		$this->response->setStatusCode(200);

		return PostLinkEx::getLink($postId, $linkType);
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
	 * Request Form
	 *
	 * This method will create a new PostLinkEx model, then set its attributes with the
	 * data from the request and the possible path parameters.
	 *
	 * @param int $postId
	 * @param int|null $linkType
	 *
	 * @return PostLinkEx
	 * @throws \yii\base\InvalidConfigException
	 */
	private function requestForm($postId, $linkType = null)
	{
		$form = new PostLinkEx();

		$form->setAttributes($this->request->getBodyParams());
		$form->post_id = $postId;

		if ($linkType) {
			$form->post_link_type = $linkType;
		}

		return $form;
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