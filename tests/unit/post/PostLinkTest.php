<?php

namespace app\test\unit\post;

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

    /**  @var \UnitTester  */
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
    protected function _after() {}

    /** @inheritdoc */
    public function _fixtures()
    {
        return [
            "post" => fixtures\PostFixture::class,
            "link" => fixtures\PostLinkFixture::class,
        ];
    }

    /**
     * todo: missing POST ID
     * todo: not existing POST ID
     * todo: missing TYPE LINK
     * todo: not existing TYPE LINK
     * todo: wrong type TYPE LINK
     * todo: missing LINK
     * todo: wrong type LINK
     */
    public function testValidation() {}

    /**
     * todo: create link with valid info
     */
    public function testCreateLink()
    {
        $this->specify("try to create a post link with invalid relations", function () {
            $this->tester->expectException(new ErrorException(PostLink::ERR_POST_NOT_FOUND), function () {
                PostLink::createLink(1000, []);
            });

            $this->tester->expectException(new ErrorException(PostLink::ERR_LINK_TYPE_NOT_FOUND), function () {
                $postId = $this->tester->grabFixture("post", "post1")->id;

                PostLink::createLink($postId, [ "post_link_type" => 1000 ]);
            });

            $this->tester->expectException(new ErrorException(PostLink::ERR_LINK_EXISTS), function () {
                $link = $this->tester->grabFixture("link", "post_link0");

                PostLink::createLink($link["post_id"], [ "post_link_type" => $link["post_link_type"] ]);
            });
        });

        $this->specify("try to create a post link with invalid model", function () {
            $postId = $this->tester->grabFixture("post", "post9")->id;
            $data   = [
                "post_link_type" => PostLinkType::GITHUB,
            ];

            $result = PostLink::createLink($postId, $data);

            $this->tester->assertEquals(PostLink::ERROR, $result["status"]);
            $this->tester->assertTrue(is_array($result[ "error" ]));
            $this->tester->assertArrayHasKey("link", $result[ "error" ]);
        });

        $this->specify("create post link", function () {
            $postId = $this->tester->grabFixture("post", "post9")->id;
            $data   = [
                "post_link_type" => PostLinkType::GITHUB,
                "link"           => $this->faker->url,
            ];

            $result = PostLink::createLink($postId, $data);

            $this->tester->assertEquals(PostLink::SUCCESS, $result["status"]);
        });
    }
}

// EOF