<?php

namespace app\models\post;

use Yii;

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
    /** @inheritdoc */
    public static function tableName()
    {
        return 'post_link';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['post_id', 'post_link_type', 'link'], 'required'],
            [['post_id', 'post_link_type'], 'integer'],
            [['link'], 'string'],
            [['post_id', 'post_link_type'], 'unique', 'targetAttribute' => ['post_id', 'post_link_type']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['post_link_type'], 'exist', 'skipOnError' => true, 'targetClass' => PostLinkType::className(), 'targetAttribute' => ['post_link_type' => 'id']],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'post_link_type' => 'Post Link Type',
            'link' => 'Link',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLinkType()
    {
        return $this->hasOne(PostLinkType::className(), ['id' => 'post_link_type']);
    }

    /**
     * @inheritdoc
     * @return PostLinkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostLinkQuery(get_called_class());
    }
}
