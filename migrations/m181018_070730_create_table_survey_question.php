<?php

use yii\db\Migration;

class m181018_070730_create_table_survey_question extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey_question}}', [
            'survey_question_id' => $this->primaryKey()->unsigned(),
            'survey_question_name' => $this->string(),
            'survey_question_descr' => $this->text(),
            'survey_question_type' => $this->tinyInteger()->unsigned(),
            'survey_question_survey_id' => $this->integer()->unsigned(),
            'survey_question_can_skip' => $this->boolean()->defaultValue('0'),
            'survey_question_show_descr' => $this->boolean()->defaultValue('0'),
            'survey_question_is_scorable' => $this->boolean()->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('fk_survey_question_to_survey_idx', '{{%survey_question}}', 'survey_question_survey_id');
        $this->createIndex('fk_survey_question_to_type_idx', '{{%survey_question}}', 'survey_question_type');
    }

    public function down()
    {
        $this->dropTable('{{%survey_question}}');
    }
}
