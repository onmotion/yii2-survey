<?php

use yii\db\Migration;

class m190918_224430_private_survey extends Migration
{
    public function up()
    {
    	$this->alterColumn('{{%survey}}', 'survey_updated_at', $this->timestamp()->null()->defaultValue(null));
    	$this->alterColumn('{{%survey}}', 'survey_expired_at', $this->timestamp()->null()->defaultValue(null));

    	$this->addColumn('{{%survey}}', 'survey_is_private', $this->boolean()->notNull()->defaultValue(false));
    	$this->addColumn('{{%survey}}', 'survey_is_visible', $this->boolean()->notNull()->defaultValue(false));

    	$this->createTable('{{%survey_restricted_user}}', [
    		'survey_restricted_user_id' => $this->primaryKey()->unsigned(),
    		'survey_restricted_user_survey_id' => $this->integer()->unsigned()->notNull(),
    		'survey_restricted_user_user_id' => $this->integer()->notNull(),
	    ]);

        $this->addForeignKey('fk_survey_restricted_user_to_survey', '{{%survey_restricted_user}}', 'survey_restricted_user_survey_id', '{{%survey}}', 'survey_id', 'CASCADE', 'CASCADE');

        try {
            $this->addForeignKey('fk_survey_restricted_user_to_user', '{{%survey_restricted_user}}', 'survey_restricted_user_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        } catch (\yii\db\Exception $e) {
        }
    }

    public function down()
    {
        $this->dropForeignKey('fk_survey_restricted_user_to_survey', '{{%survey_restricted_user}}');

        try {
            $this->dropForeignKey('fk_survey_restricted_user_to_user', '{{%survey_restricted_user}}');
        } catch (\yii\db\Exception $e) {
        }

        $this->dropTable('{{%survey_restricted_user}}');

        $this->dropColumn('{{%survey}}', 'survey_is_private');
        $this->dropColumn('{{%survey}}', 'survey_is_visible');
    }
}
