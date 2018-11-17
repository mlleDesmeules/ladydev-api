<?php

namespace app\modules\v1\admin\controllers\post;

use app\modules\v1\admin\components\ControllerAdminEx;
use app\modules\v1\admin\models\post\PostLinkTypeEx;

/**
 * Class LinkTypeController
 *
 * @package app\modules\v1\admin\controllers\post
 */
class LinkTypeController extends ControllerAdminEx
{
	/** @inheritdoc */
	public $corsMethods = [ "OPTIONS", "GET" ];

	/** @inheritdoc */
	protected function verbs()
	{
		return [
			"index" => [ "OPTIONS", "GET" ],
		];
	}

	/**
	 * Get All
	 *
	 * @return array|\yii\db\ActiveRecord[]
	 *
	 * @SWG\Get(
	 *     path="/post-links/types",
	 *     tags={ "Post Links" },
	 *
	 *     summary="Get all post link types",
	 *     description="Returns a list of post link types",
	 *
	 *     @SWG\Response(response=200, description="all post links", @SWG\Schema(ref="#/definitions/PostLinkList")),
	 *     @SWG\Response(response=401, description="user can't be authenticated",
	 *                                 @SWG\Schema(ref="#/definitions/GeneralError")),
	 * )
	 */
	public function actionIndex()
	{
		return PostLinkTypeEx::getAllEnabled();
	}
}