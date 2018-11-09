<?php
namespace app\modules\v1\admin\components\security;

use app\helpers\ArrayHelperEx;
use app\models\app\ApiClient;
use yii\base\Behavior;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class ApiClientSecurity
 *
 * @package app\modules\v1\admin\components\security
 */
class ApiClientSecurity extends Behavior
{
	const ERR_INVALID_KEY = "INVALID_API_CLIENT_KEY";
	const ERR_MISSING_KEY = "MISSING_API_CLIENT_KEY";

	/** @inheritdoc */
	public function events ()
	{
		return ArrayHelperEx::merge(parent::events(), [
			Controller::EVENT_BEFORE_ACTION => "checkApiClient",
		]);
	}
	
	/**
	 * @param $event
	 *
	 * @throws ForbiddenHttpException
	 */
	public function checkApiClient ( $event )
	{
		if ($event->action->id === "options") {
			return;
		}

		//  get request headers
		$headers = \Yii::$app->request->getHeaders();
		
		//  get API Client key
		$key = $headers->get("Api-Client");

		//  if key isn't set, throw error
		if ( empty($key) || is_null($key) ) {
			$this->error($event, self::ERR_MISSING_KEY);
			\Yii::$app->end();
		}
		
		//  check if API Client key is valid
		$apiClient = ApiClient::findAdminKey($key);
		
		if ( is_null($apiClient) ) {
			$this->error($event, self::ERR_INVALID_KEY);
			\Yii::$app->end();
		}
	}

	/**
	 * @param $event
	 * @param string $error
	 */
	protected function error($event, $error)
	{
		/** @var \yii\web\Response $response */
		$response = $event->action->controller->response;

		$response->setStatusCode(403);
		$response->data = [
			"code"  => 403,
			"error" => [
				"short_message" => $error,
				"message"       => self::getErrorMessage($error),
			],
		];
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private static function getErrorMessage($key)
	{
		$list = [
			self::ERR_INVALID_KEY => "",
			self::ERR_MISSING_KEY => "",
		];

		return ArrayHelperEx::getValue($list, $key, $key);
	}
}

//  EOF