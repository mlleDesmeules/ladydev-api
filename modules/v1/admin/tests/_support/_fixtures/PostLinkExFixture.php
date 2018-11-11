<?php

namespace app\modules\v1\admin\tests\_support\_fixtures;


use yii\test\ActiveFixture;

/**
 * Class PostLinkExFixture
 *
 * @package app\modules\v1\admin\tests\_support\_fixtures
 */
class PostLinkExFixture extends ActiveFixture
{
	public $modelClass = 'app\modules\v1\admin\models\post\PostLinkEx';
	public $dataFile   = '/app/tests/fixtures/data/post_link.php';

	public $depends = [
		'app\modules\v1\admin\tests\_support\_fixtures\PostExFixture',
	];
}