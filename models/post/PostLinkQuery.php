<?php

namespace app\models\post;

/**
 * This is the ActiveQuery class for [[PostLink]].
 *
 * @see PostLink
 */
class PostLinkQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return PostLink[]|array
     */
    public function all($db = null) { return parent::all($db); }

    /**
     * @inheritdoc
     * @return PostLink|array|null
     */
    public function one($db = null) { return parent::one($db); }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function byPost($id)
    {
        return $this->andWhere([ "post_id" => $id ]);
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function byType($id)
    {
        return $this->andWhere([ "post_link_type" => $id ]);
    }
}
