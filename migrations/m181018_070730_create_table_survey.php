<?php

use yii\db\Migration;

class m181018_070730_create_table_survey extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey}}', [
            'survey_id' => $this->primaryKey()->unsigned(),
            'survey_name' => $this->string(),
            'survey_created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'survey_updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),,
            'survey_expired_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),,
            'survey_is_pinned' => $this->boolean()->defaultValue('0'),
            'survey_is_closed' => $this->boolean()->defaultValue('0'),
            'survey_tags' => $this->string(),
            'survey_image' => $this->string(),
            'survey_created_by' => $this->integer(),
            'survey_wallet' => $this->integer()->unsigned(),
            'survey_status' => $this->integer()->unsigned(),
            'survey_descr' => $this->text(),
            'survey_time_to_pass' => $this->smallInteger()->unsigned(),
            'survey_badge_id' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('fk_survey_created_by_idx', '{{%survey}}', 'survey_created_by');
    }

    public function down()
    {
        $this->dropTable('{{%survey}}');
    }
}
