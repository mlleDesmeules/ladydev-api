<?php

namespace app\modules\v1;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
	/**
	 * @inheritdoc
	 */
	public $controllerNamespace = 'app\modules\v1\controllers';
	
	/**
	 * @inheritdoc
	 */
	public function init ()
	{
		parent::init();
		
		$this->modules = [
			'admin' => [
				'class' => 'app\modules\v1\admin\Module',
			],
		];
	}
}
