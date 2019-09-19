<?php

use yii\db\Migration;

class m181018_070730_create_table_survey_type extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%survey_type}}', [
            'survey_type_id' => $this->tinyInteger()->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'survey_type_name' => $this->string(),
            'survey_type_descr' => $this->string(),
        ], $tableOptions);

        $this->batchInsert('{{%survey_type}}', ['survey_type_name', 'survey_type_descr'], [
            ['Multiple choice', 'Ask your respondent to choose multiple answers from your list of answer choices.'],
            ['One choise of list', 'Ask your respondent to choose one answer from your list of answer choices.'],
            ['Dropdown', 'Provide a dropdown list of answer choices for respondents to choose from.'],
            ['Ranking', 'Ask respondents to rank a list of options in the order they prefer using numeric dropdown menus.'],
            ['Slider', 'Ask respondents to rate an item or question by dragging an interactive slider.'],
            ['Single textbox', 'Add a single textbox to your survey when you want respondents to write in a short text or numerical answer to your question.'],
            ['Multiple textboxes', 'Add multiple textboxes to your survey when you want respondents to write in more than one short text or numerical answer to your question.'],
            ['Comment box', 'Use the comment or essay box to collect open-ended, written feedback from respondents.'],
            ['Date/Time', 'Ask respondents to enter a specific date and/or time.'],
            ['Calendar', 'Ask respondents to choose better date/time for a meeting.']
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%survey_type}}');
    }
}
