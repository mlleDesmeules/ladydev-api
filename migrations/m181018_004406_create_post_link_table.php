<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_link`.
 * Has foreign keys to the tables:
 *
 * - `post`
 * - `lang`
 * - `post_link_type`
 */
class m181018_004406_create_post_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_link', [
            'post_id' => $this->integer()->notNull(),
            'post_link_type' => $this->integer()->notNull(),
            'link' => $this->text()->notNull(),
        ]);

        $this->addPrimaryKey("pk-post-link", "post_link", [ "post_id", "post_link_type" ]);

        // creates index for column `post_id`
        $this->createIndex(
            'idx-post_link-post_id',
            'post_link',
            'post_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-post_link-post_id',
            'post_link',
            'post_id',
            'post',
            'id',
            'CASCADE'
        );

        // creates index for column `post_link_type`
        $this->createIndex(
            'idx-post_link-post_link_type',
            'post_link',
            'post_link_type'
        );

        // add foreign key for table `post_link_type`
        $this->addForeignKey(
            'fk-post_link-post_link_type',
            'post_link',
            'post_link_type',
            'post_link_type',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `post`
        $this->dropForeignKey(
            'fk-post_link-post_id',
            'post_link'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-post_link-post_id',
            'post_link'
        );

        // drops foreign key for table `post_link_type`
        $this->dropForeignKey(
            'fk-post_link-post_link_type',
            'post_link'
        );

        // drops index for column `post_link_type`
        $this->dropIndex(
            'idx-post_link-post_link_type',
            'post_link'
        );

        $this->dropTable('post_link');
    }
}
