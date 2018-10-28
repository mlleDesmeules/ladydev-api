<?php

namespace app\tests\fixtures;

/**
 * Class PostLinkFixture
 *
 * @package app\tests\fixtures
 */
class PostLinkFixture extends \yii\test\ActiveFixture
{
    public $modelClass = 'app\models\post\PostLink';

    public $dataFile = "@tests/fixtures/data/post_link.php";

    public $depends = [
        'app\tests\fixtures\PostFixture',
    ];
}