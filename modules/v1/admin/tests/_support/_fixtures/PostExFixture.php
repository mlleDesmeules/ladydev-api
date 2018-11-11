<?php
namespace app\modules\v1\admin\tests\_support\_fixtures;

use yii\test\ActiveFixture;

/**
 * Class PostExFixture
 *
 * @package app\modules\v1\admin\tests\_support\_fixtures
 */
class PostExFixture extends ActiveFixture
{
	public $modelClass = 'app\modules\v1\admin\models\post\PostEx';
	public $dataFile   = '/app/tests/fixtures/data/post.php';

	public $depends = [
		'app\modules\v1\admin\tests\_support\_fixtures\CategoryExFixture',
	];
}
