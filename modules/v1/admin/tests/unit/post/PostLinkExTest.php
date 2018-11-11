<?php

namespace app\modules\v1\admin\tests\unit\post;

use app\models\post\PostLinkType;
use app\modules\v1\admin\models\post\PostLinkEx;
use app\modules\v1\admin\tests\_support\_fixtures as fixtures;
use Faker\Factory as Faker;

/**
 * Class PostLinkExTest
 *
 * @package app\modules\v1\admin\tests\unit\post
 */
class PostLinkExTest extends \Codeception\Test\Unit
{
	use \Codeception\Specify;

	/** @var \UnitTester */
	protected $tester;

	/** @var \Faker\Generator */
	protected $faker;

	/** @inheritdoc */
	protected function _before()
	{
		$this->faker = Faker::create();

		$this->faker->addProvider(new \Faker\Provider\Internet($this->faker));
	}

	/** @inheritdoc */
	protected function _after()
	{
	}

	/** @inheritdoc */
	public function _fixtures()
	{
		return [
			"post" => fixtures\PostExFixture::class,
			"link" => fixtures\PostLinkExFixture::class,
		];
	}

	public function testCreateLink()
	{
		$this->specify("fail to create - with invalid post id", function () {
			$result = PostLinkEx::createLink(1000, []);

			$this->errorMessage(PostLinkEx::ERR_POST_NOT_FOUND, $result);
		});
		$this->specify("fail to create - with invalid post link type", function () {
			$postId = $this->tester->grabFixture("post", "post1")->id;
			$result = PostLinkEx::createLink($postId, [
				"post_link_type" => 1000,
			]);

			$this->errorMessage(PostLinkEx::ERR_LINK_TYPE_NOT_FOUND, $result);
		});
		$this->specify("fail to create - with existing combination", function () {
			$link   = $this->tester->grabFixture("link", "post_link0");
			$result = PostLinkEx::createLink($link[ "post_id" ], [
				"post_link_type" => $link[ "post_link_type" ],
			]);

			$this->errorMessage(PostLinkEx::ERR_LINK_EXISTS, $result);
		});
		$this->specify("fail to create - with invalid model", function () {
			$postId = $this->tester->grabFixture("post", "post9")->id;
			$result = PostLinkEx::createLink($postId, [
				"post_link_type" => PostLinkType::GITHUB,
			]);

			$this->tester->assertEquals(PostLinkEx::ERROR, $result[ "status" ]);
			$this->tester->assertEquals(PostLinkEx::ERR_MODEL_INVALID, $result[ "error" ][ "short_message" ]);
			$this->tester->assertArrayHasKey("form_errors", $result[ "error" ]);
			$this->tester->assertTrue(is_array($result[ "error" ][ "form_errors" ]));
		});
		$this->specify("success to create", function () {
			$postId = $this->tester->grabFixture("post", "post9")->id;
			$result = PostLinkEx::createLink($postId, [
				"post_link_type" => PostLinkType::GITHUB,
				"link"           => $this->faker->url,
			]);

			$this->tester->assertEquals(PostLinkEx::SUCCESS, $result[ "status" ]);
		});
	}

	public function testUpdateLink()
	{
		$this->specify("fail to update - invalid post link", function () {
			$result = PostLinkEx::updateLink(1000, 0, []);

			$this->errorMessage(PostLinkEx::ERR_LINK_NOT_EXISTS, $result);
		});
		$this->specify("fail to update - invalid model", function () {
			$link = $this->tester->grabFixture("link", "post_link3");
			$data = [ "link" => 123525 ];

			$result = PostLinkEx::updateLink($link[ "post_id" ], $link[ "post_link_type" ], $data);

			$this->tester->assertEquals(PostLinkEx::ERROR, $result[ "status" ]);
			$this->tester->assertEquals(PostLinkEx::ERR_MODEL_INVALID, $result[ "error" ][ "short_message" ]);
			$this->tester->assertArrayHasKey("form_errors", $result[ "error" ]);
			$this->tester->assertTrue(is_array($result[ "error" ][ "form_errors" ]));
			$this->tester->assertArrayHasKey("link", $result[ "error" ][ "form_errors" ]);
		});
		$this->specify("success to update", function () {
			$link = $this->tester->grabFixture("link", "post_link1");
			$data = [ "link" => $this->faker->url ];

			$result = PostLinkEx::updateLink($link[ "post_id" ], $link[ "post_link_type" ], $data);

			$this->tester->assertEquals(PostLinkEx::SUCCESS, $result[ "status" ]);
		});
	}

	/**
	 * @param string $errMessage
	 * @param array $actualError
	 */
	private function errorMessage($errMessage, $actualError)
	{
		$this->tester->assertEquals(PostLinkEx::ERROR, $actualError[ "status" ]);
		$this->tester->assertEquals($errMessage, $actualError[ "error" ][ "short_message" ]);
	}
}

// EOF