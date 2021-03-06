<?php

namespace app\models\post;

use app\helpers\ArrayHelperEx;
use app\helpers\DateHelper;
use app\models\app\Lang;
use app\models\category\Category;
use app\models\tag\Tag;
use app\models\tag\AssoTagPost;
use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int    $id
 * @property int    $category_id
 * @property int    $post_status_id
 * @property int    $is_featured
 * @property int    $is_comment_enabled
 * @property string $created_on
 * @property string $updated_on
 * @property string $published_on
 *
 * Relations :
 * @property AssoTagPost[] $assoTagPosts
 * @property Tag[]         $tags
 * @property Category      $category
 * @property PostStatus    $postStatus
 * @property PostLang[]    $postLangs
 * @property Lang[]        $langs
 * @property PostLink[]    $postLinks
 */
abstract class PostBase extends \yii\db\ActiveRecord
{
	const NOT_FEATURED = 0;
	const FEATURED     = 1;

	const COMMENTS_DISABLED = 0;
	const COMMENTS_ENABLED  = 1;

	const ERROR   = 0;
	const SUCCESS = 1;

	//	possible error messages
	const ERR_ON_SAVE             = "ERR_ON_SAVE";
	const ERR_ON_DELETE           = "ERR_ON_DELETE";
	const ERR_NOT_FOUND           = "ERR_NOT_FOUND";
	const ERR_CATEGORY_NOT_FOUND  = "ERR_CATEGORY_NOT_FOUND";
	const ERR_STATUS_NOT_FOUND    = "ERR_POST_STATUS_NOT_FOUND";
	const ERR_POST_PUBLISHED      = "ERR_POST_PUBLISHED";
	const ERR_MISSING_TRANSLATION = "ERR_MISSING_TRANSLATION_ON_PUBLISHED";
	const ERR_POST_DELETE_COMMENTS = "ERR_POST_DELETE_COMMENTS";

	//	possible error messages used by rules
	const ERR_FIELD_REQUIRED    = "ERR_FIELD_VALUE_REQUIRED";
	const ERR_FIELD_TYPE        = "ERR_FIELD_VALUE_WRONG_TYPE";
	const ERR_FIELD_NOT_FOUND   = "ERR_FIELD_VALUE_NOT_FOUND";
	const ERR_FIELD_UNIQUE_LANG = "ERR_FIELD_UNIQUE_LANG";

	/** @var yii\db\Connection */
	protected static $db;

	/** @inheritdoc */
	public function init ()
	{
		parent::init();

		self::$db = Yii::$app->db;
	}

	/** @inheritdoc */
	public static function tableName () { return 'post'; }

	/** @inheritdoc */
	public function rules ()
	{
		return [
			[ "category_id", "required", "message" => self::ERR_FIELD_REQUIRED ],
			[ "category_id", "integer", "message" => self::ERR_FIELD_TYPE ],
			[
				[ 'category_id' ],
				'exist',
				'skipOnError'     => true,
				'targetClass'     => Category::className(),
				'targetAttribute' => [ 'category_id' => 'id' ],
				"message"         => self::ERR_FIELD_NOT_FOUND,
			],

			[ "post_status_id", "integer", "message" => self::ERR_FIELD_TYPE ],
			[
				[ 'post_status_id' ],
				'exist',
				'skipOnError'     => true,
				'targetClass'     => PostStatus::className(),
				'targetAttribute' => [ 'post_status_id' => 'id' ],
				"message"         => self::ERR_FIELD_NOT_FOUND,
			],

			[ "is_featured", "integer", "message" => self::ERR_FIELD_TYPE ],
			[ "is_featured", "default", "value" => self::NOT_FEATURED ],

			[ "is_comment_enabled", "integer", "message" => self::ERR_FIELD_TYPE ],
			[ "is_comment_enabled", "default", "value" => self::COMMENTS_ENABLED ],

			[ "created_on", "safe" ],
			[ "updated_on", "safe" ],
			[ "published_on", "safe" ],
		];
	}

	/** @inheritdoc */
	public function attributeLabels ()
	{
		return [
			'id'             => Yii::t('app.post', 'ID'),
			'category_id'    => Yii::t('app.post', 'Category ID'),
			'post_status_id' => Yii::t('app.post', 'Post Status ID'),
			'created_on'     => Yii::t('app.post', 'Created On'),
			'updated_on'     => Yii::t('app.post', 'Updated On'),
		];
	}

	/** @return \yii\db\ActiveQuery */
	public function getAssoTagPosts ()
	{
		return $this->hasMany(AssoTagPost::className(), [ 'post_id' => 'id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getTags ()
	{
		return $this->hasMany(Tag::className(), [ 'id' => 'tag_id' ])
		            ->viaTable('asso_tag_post', [ 'post_id' => 'id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getCategory ()
	{
		return $this->hasOne(Category::className(), [ 'id' => 'category_id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getPostStatus ()
	{
		return $this->hasOne(PostStatus::className(), [ 'id' => 'post_status_id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getPostLangs ()
	{
		return $this->hasMany(PostLang::className(), [ 'post_id' => 'id' ]);
	}

	/** @return \yii\db\ActiveQuery */
	public function getLangs ()
	{
		return $this->hasMany(Lang::className(), [ 'id' => 'lang_id' ])
		            ->viaTable('post_lang', [ 'post_id' => 'id' ]);
	}

	/** @return \yii\db\ActiveQuery	 */
	public function getPostLinks ()
	{
		return $this->hasMany(PostLink::className, [ 'post_id', 'id' ]);
	}

	/**
	 * @inheritdoc
	 * @return PostQuery the active query used by this AR class.
	 */
	public static function find ()
	{
		return new PostQuery(get_called_class());
	}

	/** @inheritdoc */
	public function beforeSave ( $insert )
	{
		switch ($insert) {
			case true:
				$this->created_on = date(DateHelper::DATETIME_FORMAT);
				break;

			case false:
				$this->updated_on = date(DateHelper::DATETIME_FORMAT);
				break;
		}

		if ($this->isAttributeChanged("post_status_id") && $this->post_status_id === PostStatus::PUBLISHED) {
			$this->published_on = date(DateHelper::DATETIME_FORMAT);
		}

		return parent::beforeSave($insert);
	}

	/**
	 * Build an array to use when returning from another method. The status will automatically
	 * set to ERROR, then $error passed in param will be associated to the error key.
	 *
	 * @param $error
	 *
	 * @return array
	 */
	public static function buildError ( $error )
	{
		return [ "status" => self::ERROR, "error" => $error ];
	}

	/**
	 * Build an array to use when returning from another method. The status will be automatically
	 * set to SUCCESS, then the $params will be merged with the array and be returned.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public static function buildSuccess ( $params )
	{
		return ArrayHelperEx::merge([ "status" => self::SUCCESS ], $params);
	}

	/**
	 * Verify if a specific post ID exists.
	 *
	 * @param int $postId
	 *
	 * @return bool
	 */
	public static function idExists ( $postId )
	{
		return self::find()->id($postId)->exists();
	}

	/**
	 * This will save the database connection so it can be easily used by the model.
	 */
	public static function defineDbConnection () { self::$db = Yii::$app->db; }
}
