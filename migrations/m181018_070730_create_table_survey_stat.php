<?php

use yii\db\Migration;

class m181018_070730_create_table_survey_stat extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey_stat}}', [
            'survey_stat_id' => $this->primaryKey()->unsigned(),
            'survey_stat_survey_id' => $this->integer()->unsigned(),
            'survey_stat_user_id' => $this->integer(),
            'survey_stat_assigned_at' => $this->timestamp(),
            'survey_stat_started_at' => $this->timestamp(),
            'survey_stat_updated_at' => $this->timestamp(),
            'survey_stat_ended_at' => $this->timestamp(),
            'survey_stat_ip' => $this->string(),
            'survey_stat_is_done' => $this->boolean()->defaultValue('0'),
            'survey_stat_hash' => $this->char(32),
        ], $tableOptions);

        $this->createIndex('fk_sas_user_idx', '{{%survey_stat}}', 'survey_stat_user_id');
        $this->createIndex('survey_stat_hash_UNIQUE', '{{%survey_stat}}', 'survey_stat_hash', true);
        $this->createIndex('fk_stat_to_survey_idx', '{{%survey_stat}}', 'survey_stat_survey_id');
    }

    public function down()
    {
        $this->dropTable('{{%survey_stat}}');
    }
}
