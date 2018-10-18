<?php

use yii\db\Migration;

class m181018_070730_create_table_survey_answer extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey_answer}}', [
            'survey_answer_id' => $this->bigPrimaryKey()->unsigned(),
            'survey_answer_question_id' => $this->integer()->unsigned(),
            'survey_answer_name' => $this->string(),
            'survey_answer_descr' => $this->text(),
            'survey_answer_class' => $this->string(),
            'survey_answer_comment' => $this->string(),
            'survey_answer_sort' => $this->integer(),
            'survey_answer_points' => $this->integer()->defaultValue('0'),
            'survey_answer_show_descr' => $this->boolean()->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('fk_survey_answer_to_question_idx', '{{%survey_answer}}', 'survey_answer_question_id');
    }

    public function down()
    {
        $this->dropTable('{{%survey_answer}}');
    }
}
