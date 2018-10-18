<?php

use yii\db\Migration;

class m181018_070730_foreign_keys extends Migration
{
    public function up()
    {
        
        $this->addForeignKey('fk_survey_answer_to_question', '{{%survey_answer}}', 'survey_answer_question_id', '{{%survey_question}}', 'survey_question_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_survey_question_to_survey', '{{%survey_question}}', 'survey_question_survey_id', '{{%survey}}', 'survey_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_survey_question_to_type', '{{%survey_question}}', 'survey_question_type', '{{%survey_type}}', 'survey_type_id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('fk_stat_to_survey', '{{%survey_stat}}', 'survey_stat_survey_id', '{{%survey}}', 'survey_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_survey_user_answer_answer', '{{%survey_user_answer}}', 'survey_user_answer_answer_id', '{{%survey_answer}}', 'survey_answer_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_survey_user_answer_question', '{{%survey_user_answer}}', 'survey_user_answer_question_id', '{{%survey_question}}', 'survey_question_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_survey_user_answer_survey', '{{%survey_user_answer}}', 'survey_user_answer_survey_id', '{{%survey}}', 'survey_id', 'CASCADE', 'CASCADE');

        try {
            $this->addForeignKey('fk_survey_created_by', '{{%survey}}', 'survey_created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
            $this->addForeignKey('fk_stat_to_user', '{{%survey_stat}}', 'survey_stat_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_survey_user_answer_user', '{{%survey_user_answer}}', 'survey_user_answer_user_id', '{{%user}}', 'id', 'SET NULL', 'SET NULL');
        } catch (\yii\db\Exception $e) {
        }

    }

    public function down()
    {
        
        $this->dropForeignKey('fk_survey_answer_to_question', '{{%survey_answer}}');
        $this->dropForeignKey('fk_survey_question_to_survey', '{{%survey_question}}');
        $this->dropForeignKey('fk_survey_question_to_type', '{{%survey_question}}');
        $this->dropForeignKey('fk_stat_to_survey', '{{%survey_stat}}');
        $this->dropForeignKey('fk_survey_user_answer_answer', '{{%survey_user_answer}}');
        $this->dropForeignKey('fk_survey_user_answer_question', '{{%survey_user_answer}}');
        $this->dropForeignKey('fk_survey_user_answer_survey', '{{%survey_user_answer}}');

        try {
            $this->dropForeignKey('fk_survey_created_by', '{{%survey}}');
            $this->dropForeignKey('fk_stat_to_user', '{{%survey_stat}}');
            $this->dropForeignKey('fk_survey_user_answer_user', '{{%survey_user_answer}}');
        } catch (\yii\db\Exception $e) {
        }
    }
}
