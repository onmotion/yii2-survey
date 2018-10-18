<?php

use yii\db\Migration;

class m181018_070730_create_table_survey_user_answer extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey_user_answer}}', [
            'survey_user_answer_id' => $this->primaryKey()->unsigned(),
            'survey_user_answer_user_id' => $this->integer(),
            'survey_user_answer_survey_id' => $this->integer()->unsigned(),
            'survey_user_answer_question_id' => $this->integer()->unsigned(),
            'survey_user_answer_answer_id' => $this->bigInteger()->unsigned(),
            'survey_user_answer_value' => $this->string(),
            'survey_user_answer_text' => $this->text(),
        ], $tableOptions);

        $this->createIndex('fk_survey_user_answer_answer_idx', '{{%survey_user_answer}}', 'survey_user_answer_answer_id');
        $this->createIndex('fk_survey_user_answer_user_idx', '{{%survey_user_answer}}', 'survey_user_answer_user_id');
        $this->createIndex('idx_answer_value', '{{%survey_user_answer}}', ['survey_user_answer_answer_id', 'survey_user_answer_value']);
        $this->createIndex('idx_question_value', '{{%survey_user_answer}}', ['survey_user_answer_question_id', 'survey_user_answer_value']);
        $this->createIndex('ff_idx', '{{%survey_user_answer}}', 'survey_user_answer_survey_id');
        $this->createIndex('fk_survey_user_answer_question_idx', '{{%survey_user_answer}}', 'survey_user_answer_question_id');
        $this->createIndex('idx_survey_user_answer_value', '{{%survey_user_answer}}', 'survey_user_answer_value');
    }

    public function down()
    {
        $this->dropTable('{{%survey_user_answer}}');
    }
}
