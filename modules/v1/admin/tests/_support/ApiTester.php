<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends \Codeception\Actor
{
	use _generated\ApiTesterActions;

	/** @var array */
	public $errorStructure = [
		"code"  => "integer",
		"error" => [
			"short_message" => "string",
			"message"       => "string",
		],
	];

	/**
	 *
	 */
	public function wantToSetApiClient ()
	{
		$token = $this->grabFromDatabase("api_client", "`key`", [ "name" => "Admin" ]);

		$this->haveHttpHeader("API-CLIENT", $token);
	}

	/**
	 *
	 */
	public function wantToBeAuthenticated ()
	{
		$token = $this->grabFromDatabase("user", "auth_token", [ "username" => "mlleDesmeules" ]);

		$this->amHttpAuthenticated($token, null);
	}

	public function wantToVerifyApiClientRequired ($action, $url)
	{
		$this->wantToBeAuthenticated();
		$this->{ "send$action" }( $url );

		$this->seeResponseCodeIs(\Codeception\Util\HttpCode::FORBIDDEN);
		$this->seeResponseMatchesJsonType($this->errorStructure);
		$this->seeResponseContainsJson([
			"code"  => 403,
			"error" => [
				"short_message" => "MISSING_API_CLIENT_KEY",
				"message"       => "",
			],
		]);
	}

	public function wantToVerifyAuthenticationRequired ($action, $url)
	{
		$this->wantToSetApiClient();
		$this->{ "send$action" }( $url );

		$this->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
		$this->seeResponseContainsJson([ "code" => 401, "message" => "Your request was made with invalid credentials." ]);
	}

	/**
	 * @param integer $code
	 * @param string $error
	 */
	public function seeResponseIsErrorMessage($code, $error)
	{
		$this->seeResponseIsJson();
		$this->seeResponseMatchesJsonType($this->errorStructure);
		$this->seeResponseContainsJson([
			"code"  => $code,
			"error" => [ "short_message" => $error ],
		]);
	}

	public function seeResponseContainsFormError($attribute, $error)
	{
		$this->seeResponseContainsJson([
			"error" => [
				"form_errors" => [ $attribute => [ $error ], ],
			],
		]);
	}
}
