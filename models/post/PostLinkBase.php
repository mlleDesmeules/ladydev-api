<?php

namespace app\models\post;

use app\helpers\ArrayHelperEx;

/**
 * This is the model class for table "post_link".
 *
 * @property int $post_id
 * @property int $post_link_type
 * @property string $link
 *
 * Relations:
 * @property Post $post
 * @property PostLinkType $postLinkType
 */
abstract class PostLinkBase extends \yii\db\ActiveRecord
{
    const ERROR   = 0;
    const SUCCESS = 1;

    // possibe error messages
	const ERR_LINK_EXISTS         = "ERR_LINK_ALREADY_EXISTS";
	const ERR_POST_NOT_FOUND      = "ERR_POST_NOT_FOUND";
	const ERR_LINK_TYPE_NOT_FOUND = "ERR_LINK_TYPE_NOT_FOUND";
	const ERR_ON_SAVE             = "ERR_ON_SAVE";
	const ERR_LINK_NOT_EXISTS     = "ERR_LINK_DOESNT_EXISTS";
	const ERR_ON_DELETE           = "ERR_ON_DELETE";
	const ERR_MODEL_INVALID       = "ERR_MODEL_INVALID";

	//	possible error messages used by rules
	const ERR_FIELD_REQUIRED   = "ERR_FIELD_VALUE_REQUIRED";
	const ERR_FIELD_TYPE       = "ERR_FIELD_VALUE_WRONG_TYPE";
	const ERR_FIELD_NOT_FOUND  = "ERR_FIELD_VALUE_NOT_FOUND";
	const ERR_FIELD_NOT_UNIQUE = "ERR_FIELD_COMBINATION_NOT_UNIQUE";

    /** @inheritdoc */
    public static function tableName()
    {
        return "post_link";
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [ "post_id", "required", "message" => self::ERR_FIELD_REQUIRED ],
            [ "post_id", "integer", "message" => self::ERR_FIELD_TYPE ],
            [
                "post_id", "exist",
                "skipOnError" => true,
                "targetClass" => Post::class,
                "targetAttribute" => ["post_id" => "id"],
	            "message" => self::ERR_FIELD_NOT_FOUND,
            ],

            [ "post_link_type", "required", "message" => self::ERR_FIELD_REQUIRED ],
            [ "post_link_type", "integer", "message" => self::ERR_FIELD_TYPE ],
            [
                "post_link_type", "exist",
                "skipOnError" => true,
                "targetClass" => PostLinkType::class,
                "targetAttribute" => ["post_link_type" => "id"],
	            "message" => self::ERR_FIELD_NOT_FOUND,
            ],

            [ "link", "required", "message" => self::ERR_FIELD_REQUIRED ],
            [ "link", "string", "message" => self::ERR_FIELD_TYPE ],

	        [
		        ["post_id", "post_link_type"], "unique",
		        "targetAttribute" => ["post_id", "post_link_type"],
		        "message" => self::ERR_FIELD_NOT_UNIQUE
	        ],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            "post_id" => "Post ID",
            "post_link_type" => "Post Link Type",
            "link" => "Link",
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ["id" => "post_id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLinkType()
    {
        return $this->hasOne(PostLinkType::className(), ["id" => "post_link_type"]);
    }

    /**
     * @inheritdoc
     * @return PostLinkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostLinkQuery(get_called_class());
    }

    /**
     * Build an array to use when returning from another method. The status will automatically
     * set to ERROR, then $error passed in param will be associated to the error key.
     *
     * @param $error
     *
     * @return array
     */
    public static function buildError($error)
    {
        return ["status" => self::ERROR, "error" => $error];
    }

    /**
     * Build an array to use when returning from another method. The status will be automatically
     * set to SUCCESS, then the $params will be merged with the array and be returned.
     *
     * @param array $params
     *
     * @return array
     */
    public static function buildSuccess($params)
    {
        return ArrayHelperEx::merge(["status" => self::SUCCESS], $params);
    }

    /**
     * @param integer $postId
     * @param integer $typeId
     *
     * @return boolean
     */
    public static function linkExists($postId, $typeId)
    {
        return self::find()->byPost($postId)->byType($typeId)->exists();
    }
}
