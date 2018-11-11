<?php

use app\modules\v1\admin\models\post\PostLinkEx;
use app\modules\v1\admin\tests\_support\_fixtures as fixtures;
use \Codeception\Util\HttpCode;
use Faker\Factory as Faker;

/**
 * Class LinkCest
 */
class LinkCest
{
	const URL = "/posts/{postId}/links";

	/** @var \Faker\Generator */
	protected $faker;

	/** @var array */
	protected $structure = [
		"post_id"   => "integer",
		"link_type" => "integer",
		"link"      => "string",
	];

	/** @inheritdoc */
	public function _before(ApiTester $I)
	{
		$this->faker = Faker::create();

		$this->faker->addProvider(new \Faker\Provider\Internet($this->faker));

		$I->haveFixtures([
			"post" => fixtures\PostExFixture::class,
			"link" => fixtures\PostLinkExFixture::class,
		]);
	}

	/** @inheritdoc */
	public function _after(ApiTester $I)
	{
	}

	private function buildUrl($postId, $linkType = null)
	{
		$url = str_replace("{postId}", $postId, self::URL);

		if ($linkType) {
			$url .= "/{$linkType}";
		}

		return $url;
	}

	public function failWithoutApiClient(ApiTester $I)
	{
		$I->wantToVerifyApiClientRequired("GET", $this->buildUrl(1000));
		$I->wantToVerifyApiClientRequired("GET", $this->buildUrl(1000, 1000));
		$I->wantToVerifyApiClientRequired("POST", $this->buildUrl(1000));
		$I->wantToVerifyApiClientRequired("PUT", $this->buildUrl(1000, 1000));
		$I->wantToVerifyApiClientRequired("DELETE", $this->buildUrl(1000, 1000));
	}

	public function failWithoutAuth(ApiTester $I)
	{
		$I->wantToVerifyAuthenticationRequired("GET", $this->buildUrl(1000));
		$I->wantToVerifyAuthenticationRequired("GET", $this->buildUrl(1000, 1000));
		$I->wantToVerifyAuthenticationRequired("POST", $this->buildUrl(1000));
		$I->wantToVerifyAuthenticationRequired("PUT", $this->buildUrl(1000, 1000));
		$I->wantToVerifyAuthenticationRequired("DELETE", $this->buildUrl(1000, 1000));
	}

	public function failGetAllPostIdNotFound(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendGET($this->buildUrl(1000));

		$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
		$I->seeResponseIsErrorMessage(HttpCode::NOT_FOUND, PostLinkEx::ERR_POST_NOT_FOUND);
	}

	public function successGetAll(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendGET($this->buildUrl(2));

		$I->seeResponseCodeIs(HttpCode::OK);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType($this->structure);
	}

	public function failCreatePostIdNotFound(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendPOST($this->buildUrl(1000), []);

		$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
		$I->seeResponseIsErrorMessage(HttpCode::NOT_FOUND, PostLinkEx::ERR_POST_NOT_FOUND);
	}

	public function failCreateInvalidModel(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$postId = $I->grabFixture("post", "post9")->id;
		$body   = [ "post_link_type" => "invalid", "link" => $this->faker->url ];

		$I->sendPOST($this->buildUrl($postId), $body);

		$I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
		$I->seeResponseIsErrorMessage(HttpCode::UNPROCESSABLE_ENTITY, PostLinkEx::ERR_MODEL_INVALID);
		$I->seeResponseContainsFormError("post_link_type", PostLinkEx::ERR_FIELD_TYPE);
	}

	public function successCreate(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$postId = $I->grabFixture("post", "post9")->id;
		$body   = [ "post_link_type" => \app\models\post\PostLinkType::GITHUB, "link" => $this->faker->url ];

		$I->sendPOST($this->buildUrl($postId), $body);

		$I->seeResponseCodeIs(HttpCode::CREATED);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType($this->structure);
		$I->seeResponseContainsJson([
			"post_id"   => $postId,
			"link_type" => $body[ "post_link_type" ],
			"link"      => $body[ "link" ],
		]);
	}

	public function failGetOneLinkNotFound(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendGET($this->buildUrl(1000, 1000));

		$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
		$I->seeResponseIsErrorMessage(HttpCode::NOT_FOUND, PostLinkEx::ERR_LINK_NOT_EXISTS);
	}

	public function successGetOne(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$link = $I->grabFixture("link", "post_link2");

		$I->sendGet($this->buildUrl($link[ "post_id" ], $link[ "post_link_type" ]));

		$I->seeResponseCodeIs(HttpCode::OK);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType($this->structure);
	}

	public function failUpdateLinkNotFound(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$I->sendPut($this->buildUrl(1000, 1000), []);

		$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
		$I->seeResponseIsErrorMessage(HttpCode::NOT_FOUND, PostLinkEx::ERR_LINK_NOT_EXISTS);
	}

	public function failUpdateInvalidModel(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$link = $I->grabFixture("link", "post_link2");
		$body = [ "link" => null ];

		$I->sendPUT($this->buildUrl($link[ "post_id" ], $link[ "post_link_type" ]), $body);

		$I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
		$I->seeResponseIsErrorMessage(HttpCode::UNPROCESSABLE_ENTITY, PostLinkEx::ERR_MODEL_INVALID);
		$I->seeResponseContainsFormError("link", PostLinkEx::ERR_FIELD_REQUIRED);
	}

	public function successUpdate(ApiTester $I)
	{
		$I->wantToSetApiClient();
		$I->wantToBeAuthenticated();

		$link = $I->grabFixture("link", "post_link2");
		$body = [ "link" => $this->faker->url ];

		$I->sendPUT($this->buildUrl($link[ "post_id" ], $link[ "post_link_type" ]), $body);

		$I->seeResponseCodeIs(HttpCode::OK);
		$I->seeResponseIsJson();
		$I->seeResponseMatchesJsonType($this->structure);
		$I->seeResponseContainsJson([
			"post_id"   => $link[ "post_id" ],
			"link_type" => $link[ "post_link_type" ],
			"link"      => $body[ "link" ],
		]);
	}
}
