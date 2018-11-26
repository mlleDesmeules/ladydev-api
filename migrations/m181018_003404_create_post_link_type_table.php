<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_link_type`.
 */
class m181018_003404_create_post_link_type_table extends Migration
{
	/** @inheritdoc */
	public function safeUp()
	{
		$this->createTable("post_link_type", [
			"id"          => $this->primaryKey(),
			"name"        => $this->string(100)->notNull(),
			"description" => $this->string(255),
			"is_enabled"  => $this->integer(1)->defaultValue(1),
		]);

		$this->insert("post_link_type", [
			"name"        => "Source Code",
			"description" => "Link to the complete source code",
		]);

		$this->insert("post_link_type", [
			"name"        => "Demo",
			"description" => "Link to a working demo",
		]);
	}

	/** @inheritdoc */
	public function safeDown()
	{
		$this->dropTable("post_link_type");
	}
}
