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
}
