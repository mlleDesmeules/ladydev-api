<?php

/**
 * Class LinkTypeCest
 * @package post
 */
class LinkTypeCest
{
	const URL = "/post-links/types";

	/** @var array */
	protected $structure = [
		"id"          => "integer",
		"name"        => "string",
		"description" => "string",
	];

	/** @inheritdoc */
	public function _before(ApiTester $I)
	{
	}

	/** @inheritdoc */
	public function _after(ApiTester $I)
	{
	}

	public function failWithoutApiClient(ApiTester $I)
	{
		$I->wantToVerifyApiClientRequired("GET", self::URL);
	}

	public function failWithoutAuth(ApiTester $I)
	{
		$I->wantToVerifyAuthenticationRequired("GET", self::URL);
	}

	public function successGetAll(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendGET(self::URL);

		$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType($this->structure);
	}
}

//  EOF