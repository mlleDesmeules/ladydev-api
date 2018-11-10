<?php

namespace app\test\unit\post;

use app\models\post\Post;
use app\models\post\PostLink;
use app\models\post\PostLinkType;
use app\tests\fixtures;
use Faker\Factory as Faker;
use yii\base\ErrorException;

/**
 * Class PostLinkTest
 *
 * @package app\test\unit\post
 */
class PostLinkTest extends \Codeception\Test\Unit
{
	use \Codeception\Specify;

	/**  @var \UnitTester */
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
			"post" => fixtures\PostFixture::class,
			"link" => fixtures\PostLinkFixture::class,
		];
	}

	public function testValidation()
	{
		$this->specify("post_id is required", function () {
			$model = new PostLink();

			$this->tester->assertFalse($model->validate(["post_id"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_REQUIRED, $model->getErrors("post_id"));
		});
		$this->specify("post_id is expected to be an integer", function () {
			$model = new PostLink(["post_id" => "invalid"]);

			$this->tester->assertFalse($model->validate(["post_id"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_TYPE, $model->getErrors("post_id"));
		});
		$this->specify("post_id is expected to exists in post table", function () {
			$model = new PostLink(["post_id" => 1000]);

			$this->tester->assertFalse($model->validate(["post_id"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_NOT_FOUND, $model->getErrors("post_id"));
		});

		$this->specify("post_link_type is required", function () {
			$model = new PostLink();

			$this->tester->assertFalse($model->validate(["post_link_type"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_REQUIRED, $model->getErrors("post_link_type"));
		});
		$this->specify("post_link_type is expected to be an integer", function () {
			$model = new PostLink(["post_link_type" => "github"]);

			$this->tester->assertFalse($model->validate(["post_link_type"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_TYPE, $model->getErrors("post_link_type"));
		});
		$this->specify("post_link_type is expected to exists in post link type table", function () {
			$model = new PostLink(["post_link_type" => 1000]);

			$this->tester->assertFalse($model->validate(["post_link_type"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_NOT_FOUND, $model->getErrors("post_link_type"));
		});

		$this->specify("link is required", function () {
			$model = new PostLink();

			$this->tester->assertFalse($model->validate(["link"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_REQUIRED, $model->getErrors("link"));
		});
		$this->specify("link is expected to be an string", function () {
			$model = new PostLink(["link" => 123]);

			$this->tester->assertFalse($model->validate(["link"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_TYPE, $model->getErrors("link"));
		});

		$this->specify("post ID and post link type combination must be unique", function () {
			$model = new PostLink([
				"post_id" => $this->tester->grabFixture("post", "post0")->id,
				"post_link_type" => PostLinkType::GITHUB,
			]);

			$this->tester->assertFalse($model->validate(["post_id", "post_link_type"]));
			$this->tester->assertContains(PostLink::ERR_FIELD_NOT_UNIQUE, $model->getErrors("post_id"));
			$this->tester->assertContains(PostLink::ERR_FIELD_NOT_UNIQUE, $model->getErrors("post_link_type"));
		});

		$this->specify("valid post link model", function () {
			$model = new PostLink([
				"post_id" => $this->tester->grabFixture("post", "post9")->id,
				"post_link_type" => PostLinkType::GITHUB,
				"link" => $this->faker->url,
			]);

			$this->tester->assertTrue($model->validate());
		});
	}

	public function testCreateLink()
	{
		$this->specify("try to create a post link with invalid relations", function () {
			$this->tester->expectException(new ErrorException(PostLink::ERR_POST_NOT_FOUND), function () {
				PostLink::createLink(1000, []);
			});

			$this->tester->expectException(new ErrorException(PostLink::ERR_LINK_TYPE_NOT_FOUND), function () {
				$postId = $this->tester->grabFixture("post", "post1")->id;

				PostLink::createLink($postId, ["post_link_type" => 1000]);
			});

			$this->tester->expectException(new ErrorException(PostLink::ERR_LINK_EXISTS), function () {
				$link = $this->tester->grabFixture("link", "post_link0");

				PostLink::createLink($link["post_id"], ["post_link_type" => $link["post_link_type"]]);
			});
		});

		$this->specify("try to create a post link with invalid model", function () {
			$postId = $this->tester->grabFixture("post", "post9")->id;
			$data = [
				"post_link_type" => PostLinkType::GITHUB,
			];

			$result = PostLink::createLink($postId, $data);

			$this->tester->assertEquals(PostLink::ERROR, $result["status"]);
			$this->tester->assertTrue(is_array($result["error"]));
			$this->tester->assertArrayHasKey("link", $result["error"]);
		});

		$this->specify("create post link", function () {
			$postId = $this->tester->grabFixture("post", "post9")->id;
			$data = [
				"post_link_type" => PostLinkType::GITHUB,
				"link" => $this->faker->url,
			];

			$result = PostLink::createLink($postId, $data);

			$this->tester->assertEquals(PostLink::SUCCESS, $result["status"]);
			$this->tester->assertEquals($postId, $result[ "post_id" ]);
			$this->tester->assertEquals($data[ "post_link_type" ], $result[ "post_link_type" ]);
		});
	}

	public function testUpdateLink()
	{
		$this->specify("try to update not existing link", function () {
			$this->tester->expectException(new ErrorException(PostLink::ERR_LINK_NOT_EXISTS), function () {
				$postId = $this->tester->grabFixture("post", "post7")->id;

				PostLink::updateLink($postId, PostLinkType::GITHUB, ["link" => $this->faker->url]);
			});
		});

		$this->specify("try to update link with invalid model", function () {
			$link = $this->tester->grabFixture("link", "post_link1");
			$data = [ "link" => 123432 ];

			$result = PostLink::updateLink($link[ "post_id" ], $link[ "post_link_type" ], $data);

			$this->tester->assertEquals(PostLink::ERROR, $result[ "status" ]);
			$this->tester->assertTrue(is_array($result[ "error" ]));
			$this->tester->assertArrayHasKey("link", $result[ "error" ]);
		});

		$this->specify("update a post link", function () {
			$link = $this->tester->grabFixture("link", "post_link1");
			$data = [ "link" => $this->faker->url ];

			$result = PostLink::updateLink($link[ "post_id" ], $link[ "post_link_type" ], $data);

			$this->tester->assertEquals(PostLink::SUCCESS, $result[ "status" ]);
		});
	}

	public function testDeleteLink()
	{
		$this->specify("delete not existing post link", function () {
			$this->tester->expectException(new ErrorException(PostLink::ERR_LINK_NOT_EXISTS), function () {
				PostLink::deleteLink(7, PostLinkType::GITHUB);
			});
		});

		$this->specify("delete published post link", function () {
			$this->tester->expectException(new ErrorException(Post::ERR_POST_PUBLISHED), function () {
				$link = $this->tester->grabFixture("link", "post_link3");

				PostLink::deleteLink($link["post_id"], $link["post_link_type"]);
			});
		});

		$this->specify("delete post link", function () {
			$link = $this->tester->grabFixture("link", "post_link0");

			PostLink::deleteLink($link["post_id"], $link["post_link_type"]);
		});
	}
}

// EOF