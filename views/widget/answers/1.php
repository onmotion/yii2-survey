<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use onmotion\survey\models\SurveyUserAnswer;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

$userAnswers = $question->userAnswers;

foreach ($question->answers as $i => $answer) {
    $userAnswer = $userAnswers[$answer->survey_answer_id] ?? (new SurveyUserAnswer());

    $label = '<div class="text"><div class="name">' . $answer->survey_answer_name . '</div>';


    if ($answer->survey_answer_show_descr) {
        $label .=  "<div class='answer-description'>$answer->survey_answer_descr</div>";
    }
    $label .= '</div>';

    echo $form->field($userAnswer, "[$question->survey_question_id][$answer->survey_answer_id]survey_user_answer_value",
        [
            'template' => "<div class='survey-questions-form-field checkbox-group'>{input}{label}</div>\n{hint}\n{error}",
            'labelOptions' => ['class' => 'css-label', 'label' => $label],
        ]
    )->checkbox(['class' => 'css-checkbox'], false);

    echo Html::tag('br', '');
}