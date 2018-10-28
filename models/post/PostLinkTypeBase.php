<?php

namespace app\models\post;

use Yii;

/**
 * This is the model class for table "post_link_type".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $is_enabled
 *
 * Relations:
 * @property PostLink[] $postLinks
 * @property Post[] $posts
 */
class PostLinkTypeBase extends \yii\db\ActiveRecord
{
    /** @inheritdoc */
    public static function tableName() { return 'post_link_type'; }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_enabled'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'is_enabled' => 'Is Enabled',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostLinks()
    {
        return $this->hasMany(PostLink::className(), ['post_link_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['id' => 'post_id'])->viaTable('post_link', ['post_link_type' => 'id']);
    }

    /**
     * Verify if a specific ID exists
     *
     * @return bool
     */
    public static function idExists($id)
    {
        return self::find()->andWhere([ "id" => $id ])->exists();
    }
}
